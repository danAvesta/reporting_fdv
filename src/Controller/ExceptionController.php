<?php
// src/Controller/ExceptionController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ExceptionController
{
    public function showException(\Throwable $exception): Response
    {
        if ($exception instanceof AccessDeniedException) {
            return new Response($content = $this->renderView('templates/admin/error_permission.html.twig'), 403);
        }

        // Gérez les autres types d'exceptions si nécessaire

        return new Response('Vous n\'avez pas la permission d\'accéder à cette ressource.', 500);
    }
}
