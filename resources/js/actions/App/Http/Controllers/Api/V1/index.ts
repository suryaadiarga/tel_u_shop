import CatalogController from './CatalogController'
import OrderController from './OrderController'


const V1 = {
    CatalogController: Object.assign(CatalogController, CatalogController),
    OrderController: Object.assign(OrderController, OrderController),
}

export default V1