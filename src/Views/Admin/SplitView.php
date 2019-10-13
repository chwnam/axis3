<?php

namespace Shoplic\Axis3\Views\Admin;

use Shoplic\Axis3\Views\BaseView;
use function Shoplic\Axis3\Functions\getCleanUrlPath;

abstract class SplitView extends BaseView
{
    private $items = [];

    private $allowedParams = [];

    private $param = '';

    private $template = '';

    private $baseUrl = '';

    public function dispatch()
    {
        $this->renderItems();

        $current = $this->getCurrent();
        $items   = $this->getItems();

        if ($items && isset($items[$current])) {
            $view = $items[$current]['view'] ?? null;
            if (is_array($view) && sizeof($view) == 2) {
                $object = $this->claimView($view[0], $items[$current]['args'], $items[$current]['reuse']);
                if ($object) {
                    call_user_func([$object, $view[1]]);
                }
            } elseif (is_callable($view)) {
                call_user_func($view);
            }
        }
    }

    public function addItem(string $slug, string $label, $view, array $args = [], bool $reuse = true): SplitView
    {
        $slug = sanitize_key($slug);

        if ($slug) {
            $this->items[$slug] = [
                'label' => $label,
                'view'  => $view,
                'args'  => $args,
                'reuse' => $reuse,
            ];
        }

        return $this;
    }

    public function getCurrent(): string
    {
        $param = $this->getParam();
        $items = $this->getItems();

        return isset($_GET[$param]) ? sanitize_key($_GET[$param]) : key($items);
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate(string $template): SplitView
    {
        $this->template = $template;

        return $this;
    }

    public function getBaseUrl(): string
    {
        if (!$this->baseUrl) {
            $this->setBaseUrl($_SERVER['REQUEST_URI'] ?? null);
        }

        return $this->baseUrl;
    }

    public function setBaseUrl($url): SplitView
    {
        $query   = parse_url($url, PHP_URL_QUERY);
        $baseUrl = getCleanUrlPath($url);

        if ($query) {
            $params = [];
            parse_str($query, $params);
            $this->baseUrl = add_query_arg(array_intersect_key($params, $this->getAllowedParams()), $baseUrl);
        } else {
            $this->baseUrl = add_query_arg($this->getAllowedParams(), $baseUrl);
        }

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function addAllowedParam(string $param, string $value = ''): SplitView
    {
        $param = sanitize_key($param);
        $value = sanitize_key($value);

        if ($param && $param !== $this->getParam()) {
            $this->allowedParams[$param] = $value;
        }

        return $this;
    }

    public function removeAllowedParam(string $param): SplitView
    {
        if (isset($this->allowedParams[$param])) {
            unset($this->allowedParams[$param]);
        }

        return $this;
    }

    public function getAllowedParams(): array
    {
        return $this->allowedParams;
    }

    public function getParam(): string
    {
        return $this->param;
    }

    public function setParam(string $param): SplitView
    {
        $param = sanitize_key($param);
        if ($param) {
            if (isset($this->allowedParams[$param])) {
                unset($this->allowedParams[$param]);
            }
            $this->param = $param;
        }

        return $this;
    }

    abstract protected function renderItems();
}
