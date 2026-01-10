<?php

namespace App\Http\Resources;

use App\Core\Application\DTOs\RegisterOutputDTO;
use App\Core\Application\DTOs\LoginOutputDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use DateTimeInterface;

/**
 * AuthResource - formats authentication response (register/login).
 * Contains user data and access token for client-side authentication.
 */
class AuthResource extends JsonResource
{
    /**
     * Create a new resource instance.
     */
    public function __construct(
        private readonly RegisterOutputDTO|LoginOutputDTO $data
    ) {
        parent::__construct($data);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->data->user),
            'token' => [
                'type' => 'Bearer',
                'token' => $this->data->plainTextToken,
                'expires_at' => $this->data->token->getExpiresAt()->format(DateTimeInterface::ATOM),
            ],
        ];
    }
}

