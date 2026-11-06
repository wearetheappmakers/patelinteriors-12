<?php

namespace App\Routing;

use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;
use Illuminate\Support\Arr;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class UrlGenerator extends BaseUrlGenerator
{
    /**
     * Map of last registered routes by name for exact Laravel 5.8 duplicate resolution.
     *
     * @var array
     */
    protected $lastNamedRoutes = null;

    /**
     * Get the Route URL generator instance.
     *
     * @return \App\Routing\RouteUrlGenerator
     */
    protected function routeUrl()
    {
        if (! $this->routeGenerator) {
            $this->routeGenerator = new RouteUrlGenerator($this, $this->request);
        }

        return $this->routeGenerator;
    }

    /**
     * Get the URL to a named route (matching Laravel 5.8 duplicate resolution and parameter handling).
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function route($name, $parameters = [], $absolute = true)
    {
        $parameters = Arr::wrap($parameters);

        if (! is_null($route = $this->getRouteByName($name))) {
            return $this->toRoute($route, $parameters, $absolute);
        }

        throw new RouteNotFoundException("Route [{$name}] not defined.");
    }

    /**
     * Get the route instance by its name (in Laravel 5.8, last route registered with $name wins).
     *
     * @param  string  $name
     * @return \Illuminate\Routing\Route|null
     */
    protected function getRouteByName($name)
    {
        if ($this->lastNamedRoutes === null) {
            $this->lastNamedRoutes = [];
            foreach ($this->routes->getRoutes() as $r) {
                if ($rName = $r->getName()) {
                    $this->lastNamedRoutes[$rName] = $r;
                }
            }
        }

        return $this->lastNamedRoutes[$name] ?? $this->routes->getByName($name);
    }

    /**
     * Get the URL for a given route instance.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    public function toRoute($route, $parameters, $absolute)
    {
        return $this->routeUrl()->to(
            $route, Arr::wrap($parameters), $absolute
        );
    }
}
