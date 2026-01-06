<?php

namespace App\Core\Domain\Repositories;

use App\Core\Domain\Entities\Token;
use App\Core\Domain\VO\TokenId;
use App\Core\Domain\VO\UserId;

interface TokenRepository
{
    /**
     * Save or persist a token entity.
     *
     * @param Token $token The token to save
     */
    public function save(Token $token): void;

    /**
     * Retrieve a token by its ID.
     * Throws TokenNotFoundException if not found.
     *
     * @param TokenId $id The token ID
     * @return Token The token
     * @throws \App\Core\Domain\Exceptions\TokenNotFoundException
     */
    public function findById(TokenId $id): Token;

    /**
     * Retrieve a token by its plain text value.
     * Throws TokenNotFoundException if not found.
     *
     * @param string $plainTextToken The plain text token
     * @return Token The token
     * @throws \App\Core\Domain\Exceptions\TokenNotFoundException
     */
    public function getByPlainTextToken(string $plainTextToken): Token;

    /**
     * Retrieve all tokens for a specific user.
     *
     * @param UserId $userId The user ID
     * @return list<Token> Array of tokens (empty array if none found)
     */
    public function getByUserId(UserId $userId): array;

    /**
     * Delete a token by its ID.
     *
     * @param TokenId $id The token ID to delete
     */
    public function deleteById(TokenId $id): void;
}
