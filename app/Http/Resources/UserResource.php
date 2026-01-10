<?php

namespace App\Http\Resources;

use App\Core\Domain\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * UserResource - formats user data for API responses.
 */
class UserResource extends JsonResource
{
    /**
     * Create a new resource instance.
     */
    public function __construct(private readonly User $user)
    {
        parent::__construct($user);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->user->getId()->value(),
            'username' => $this->user->getUsername(),
            'email' => $this->user->getEmail()->value(),
            'email_verified' => $this->user->isEmailVerified(),
            'email_verified_at' => $this->user->getEmailVerifiedAt()?->format(\DateTimeInterface::ATOM),
            'created_at' => $this->user->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updated_at' => $this->user->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}

