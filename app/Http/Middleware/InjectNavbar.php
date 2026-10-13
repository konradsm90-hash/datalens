<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectNavbar
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Doklejamy menu tylko do stron HTML
        if ($response->headers->has('Content-Type') && str_contains($response->headers->get('Content-Type'), 'text/html')) {
            $content = $response->getContent();
            
            // Renderujemy nasze menu i wstawiamy je zaraz po tagu <body>
            if (str_contains($content, '<body')) {
                $nav = view('partials.nav')->render();
                $content = preg_replace('/(<body[^>]*>)/i', '$1' . $nav, $content);
                $response->setContent($content);
            }
        }

        return $response;
    }
}