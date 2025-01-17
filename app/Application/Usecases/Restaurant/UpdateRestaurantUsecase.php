<?php

namespace App\Application\Usecases\Restaurant;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Domain\Entities\Restaurant;
use App\Domain\Repositories\RestaurantRepositoryInterface;
use App\Application\Usecases\File\CreateFileUsecase;
use App\Application\Usecases\File\DeleteFileUsecase;
use App\Exceptions\UnauthorizedException;
use App\Services\S3Service;
use Illuminate\Support\Str;
use App\Domain\Repositories\UserRepositoryInterface;

class UpdateRestaurantUsecase
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository,
        private CreateFileUsecase $createFileUsecase,
        private DeleteFileUsecase $deleteFileUsecase,
        private S3Service $s3Service,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(string $restaurantId, array $data): Restaurant
    {
        // Démarrer une transaction
        return DB::transaction(function () use ($restaurantId, $data) {
            try {
                // 1. Auth
                $user = Auth::user();
                if (!$user) {
                    throw new UnauthorizedException("User not authenticated.");
                }

                // 2. Retrouver le restaurant
                $restaurant = $this->restaurantRepository->findById($restaurantId);
                if (!$restaurant) {
                    throw new \Exception("Restaurant not found.");
                }

                // 3. Vérifier les permissions
                if (!in_array($user->role, ['admin', 'franchise_manager'])) {
                    if ($restaurant->getOwnerId() !== $user->id) {
                        throw new UnauthorizedException("You do not have permission to update this restaurant.");
                    }
                }

                // Variables pour le rollback si nécessaire
                $oldLogoId = $restaurant->getLogoId();
                $newLogoId = null;
                $newLogoPath = null;

                // 4. Gérer le logo
                if (isset($data['logo'])) {
                    try {
                        // Générer un UUID unique pour le dossier
                        $folderUuid = Str::uuid()->toString();
                        $securePath = "restaurants/logos/{$folderUuid}";
                        
                        // 1. Créer le nouveau fichier dans le dossier sécurisé
                        $file = $this->createFileUsecase->execute($data['logo'], $securePath);
                        $newLogoId = $file->getId();
                        $newLogoPath = $file->getPath();
                        
                        // 2. Stocker l'ancien ID pour suppression ultérieure
                        $oldLogoId = $restaurant->getLogoId();
                        
                        // 3. Mettre à jour la référence dans le restaurant
                        $restaurant->setLogoId($newLogoId);
                        $this->restaurantRepository->update($restaurant);
                        
                        // 4. Une fois la référence mise à jour, supprimer l'ancien logo
                        if ($oldLogoId) {
                            $this->deleteFileUsecase->execute($oldLogoId);
                        }
                    } catch (\Exception $e) {
                        // En cas d'erreur, nettoyer le nouveau fichier si créé
                        if ($newLogoPath) {
                            $this->s3Service->deleteFile($newLogoPath);
                        }
                        throw $e;
                    }
                } elseif (isset($data['remove_logo']) && $data['remove_logo'] === 'true') {
                    if ($oldLogoId = $restaurant->getLogoId()) {
                        // 1. D'abord mettre à null la référence dans le restaurant
                        $restaurant->setLogoId(null);
                        $this->restaurantRepository->update($restaurant);
                        
                        // 2. Ensuite supprimer le fichier
                        $this->deleteFileUsecase->execute($oldLogoId);
                    }
                }

                // 5. Mettre à jour les autres champs
                if (isset($data['name'])) {
                    $restaurant->setName($data['name']);
                    
                    // Mettre à jour le nom de l'utilisateur si c'est le propriétaire
                    if ($restaurant->getOwnerId() === $user->id) {
                        // Créer une entité User du domaine
                        $userEntity = new \App\Domain\Entities\User(
                            id: $user->id,
                            name: $data['name'],
                            email: $user->email,
                            role: $user->role,
                            userPlan: $user->user_plan,
                            userSubscriptionStatus: $user->user_subscription_status
                        );
                        $this->userRepository->update($userEntity);
                    }
                }
                if (isset($data['address'])) {
                    $restaurant->setAddress($data['address']);
                }
                if (isset($data['phone'])) {
                    $restaurant->setPhone($data['phone']);
                }
                if (isset($data['social_links'])) {
                    $restaurant->setSocialLinks($data['social_links']);
                }
                if (isset($data['google_info'])) {
                    $restaurant->setGoogleInfo($data['google_info']);
                }

                // 6. Sauvegarder
                return $this->restaurantRepository->update($restaurant);
            } catch (\Exception $e) {
                // En cas d'erreur, la transaction sera automatiquement annulée
                throw $e;
            }
        });
    }
} 