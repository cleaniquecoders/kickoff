<?php

use App\Actions\Builder\Menu;
use App\Actions\Builder\MenuItem;
use Illuminate\Http\Request;

function visitPath(string $path): void
{
    app()->instance('request', Request::create($path));
}

function menuItem(string $url): MenuItem
{
    return (new MenuItem)->setLabel('Item')->setUrl(url($url));
}

it('throws on an unknown builder key', function () {
    Menu::make()->build('not-a-builder');
})->throws(InvalidArgumentException::class, 'Unknown menu builder [not-a-builder].');

it('builds a parent item that has no url', function () {
    $parent = (new MenuItem)->setLabel('Group')->addChild(menuItem('/a'));

    expect($parent->build()->toArray())
        ->url->toBe('')
        ->children->toHaveCount(1);
});

it('drops a group whose children are all hidden', function () {
    $parent = (new MenuItem)->setLabel('Group')->setUrl('#')
        ->addChild(menuItem('/a')->setVisible(false));

    expect($parent->build()->toArray())->toBe([]);
});

it('keeps a linked parent whose children are all hidden', function () {
    $parent = menuItem('/a')->addChild(menuItem('/a/b')->setVisible(false));

    expect($parent->build()->toArray())->children->toBe([]);
});

it('re-indexes children after hiding one', function () {
    $parent = (new MenuItem)->setLabel('Group')
        ->addChild(menuItem('/a')->setVisible(false))
        ->addChild(menuItem('/b'));

    expect(array_keys($parent->build()->toArray()['children']))->toBe([0]);
});

it('marks an index active on its detail pages, respecting the path boundary', function (string $path, bool $active) {
    visitPath($path);

    expect(menuItem('/billing')->build()->toArray()['active'])->toBe($active);
})->with([
    'itself' => ['/billing', true],
    'detail page' => ['/billing/42', true],
    'sibling sharing a prefix' => ['/billing-history', false],
]);

it('keeps exactMatch items to their own url', function () {
    visitPath('/admin/roles');

    expect(menuItem('/admin')->exactMatch()->build()->toArray()['active'])->toBeFalse();
});

it('never lets the site root own every page', function () {
    visitPath('/dashboard');

    expect(menuItem('/')->build()->toArray()['active'])->toBeFalse();
});
