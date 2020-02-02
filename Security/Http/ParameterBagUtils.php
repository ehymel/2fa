<?php

declare(strict_types=1);

namespace Scheb\TwoFactorBundle\Security\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ParameterBagUtils
{
    private static $propertyAccessor;

    /**
     * @see Symfony\Component\Security\Http\ParameterBagUtils
     *
     * Returns a request "parameter" value.
     *
     * Paths like foo[bar] will be evaluated to find deeper items in nested data structures.
     *
     * @param Request $request The request
     * @param string  $path    The key
     *
     * @throws \InvalidArgumentException when the given path is malformed
     */
    public static function getRequestParameterValue(Request $request, string $path): ?string
    {
        if (false === $pos = strpos($path, '[')) {
            return $request->get($path);
        }

        $root = substr($path, 0, $pos);

        if (null === $value = $request->get($root)) {
            return null;
        }

        if (null === self::$propertyAccessor) {
            self::$propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        try {
            return self::$propertyAccessor->getValue($value, substr($path, $pos));
        } catch (AccessException $e) {
            return null;
        }
    }
}
