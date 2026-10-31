<?php

namespace app\controllers;

use yii\base\BaseObject;
use yii\web\UrlRuleInterface;

class CustomRule extends BaseObject implements UrlRuleInterface
{
    public function createUrl($manager, $route, $params)
    {
        //check case create url with: Url::to()
//        if (preg_match('/^(app\/survey\/detail)|(app\/user\/survey)/', $route)) {
//            $routes = explode('/', $route);
//            //get slug
//            $slug = hash('md5', date("Y-m-d H:i:s")); //fake slug
//
//            if (isset($params['slug'])) {
//                $slug = $params['slug'];
//                unset($params['slug']);
//            }
//
//            //create new route
//            $newRoute = 'cms/hero/' . $slug;
//            for ($i = 2; $i < count($routes); $i++) {
//                $newRoute .= '/' . $routes[$i];
//            }
//
//            $url = self::createUrlFromRouteAndParams($newRoute, $params);
//            return $url;
//        }
        return false; // this rule does not apply
    }

    public function createUrlFromRouteAndParams($route, $params)
    {
        $url = $route;
        if ($params) {
            array_walk($params, function (&$val, $key) {
                $val = "$key=$val";
            });
            $valUrl = implode('&', $params);
            $url = $route . '?' . $valUrl;
        }
        return $url;
    }

    public function parseRequest($manager, $request)
    {
//        $pathInfo = $request->getPathInfo();
//        $params = $request->getQueryParams();
//        if (preg_match('/^(app\/survey\/detail)|(app\/survey\/edit)|(app\/survey\/identify-address)|(cms\/survey\/detail)|(cms\/survey\/edit)|(cms\/survey\/receive)/', $pathInfo)) {
//            $pathItems = explode('/', $pathInfo);
//            $params['slug'] = end($pathItems);
//            array_pop($pathItems);
//            return [implode('/', $pathItems), $params];
//        }

        return false; // this rule does not apply
    }
}
