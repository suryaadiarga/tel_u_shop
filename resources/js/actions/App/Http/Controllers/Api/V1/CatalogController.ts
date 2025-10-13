import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\V1\CatalogController::index
* @see app/Http/Controllers/Api/V1/CatalogController.php:15
* @route '/api/v1/catalog'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/catalog',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\V1\CatalogController::index
* @see app/Http/Controllers/Api/V1/CatalogController.php:15
* @route '/api/v1/catalog'
*/
index.url = (options?: RouteQueryOptions) => {




    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\CatalogController::index
* @see app/Http/Controllers/Api/V1/CatalogController.php:15
* @route '/api/v1/catalog'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\V1\CatalogController::index
* @see app/Http/Controllers/Api/V1/CatalogController.php:15
* @route '/api/v1/catalog'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\V1\CatalogController::index
* @see app/Http/Controllers/Api/V1/CatalogController.php:15
* @route '/api/v1/catalog'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\V1\CatalogController::index
* @see app/Http/Controllers/Api/V1/CatalogController.php:15
* @route '/api/v1/catalog'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\V1\CatalogController::index
* @see app/Http/Controllers/Api/V1/CatalogController.php:15
* @route '/api/v1/catalog'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

const CatalogController = { index }

export default CatalogController