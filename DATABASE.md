# ZidShip Database Design

## Database Schema

### Models:

#### 1. Courier

-   **Attributes**: `name`, `max_capacity`, `supports_cancellation`, `current_usage`
-   **Relationships**:
    -   Has many `CourierRoute`
    -   Has many `Shipment`
    -   Belongs to many `Retailer` with pivot attributes `api_key`, `account_id`

#### 2. CourierRoute

-   **Attributes**: `courier_id`, `origin`, `destination`
-   **Relationships**:
    -   Belongs to `Courier`
    -   Has many `Pricing`
    -   Has many `Shipment`

#### 3. DeliveryType

-   **Attributes**: `name`
-   **Relationships**:
    -   Has many `Pricing`
    -   Has many `Shipment`

#### 4. Package

-   **Attributes**: `height`, `width`, `length`, `weight`, `description`
-   **Relationships**:
    -   Has one `Shipment`

#### 5. Pricing

-   **Attributes**: `courier_route_id`, `delivery_type_id`, `price`
-   **Relationships**:
    -   Belongs to `CourierRoute`
    -   Belongs to `DeliveryType`

#### 6. Retailer

-   **Attributes**: `name`, `address`, `phone`, `city`, `email`
-   **Relationships**:
    -   Belongs to many `Courier` with pivot attributes `api_key`, `account_id`
    -   Has many `Shipments`

#### 7. RetailerCourierCredentials (Pivot Model)

-   **Attributes**: `retailer_id`, `courier_id`, `api_key`, `account_id`

#### 8. Shipment

-   **Attributes**: `courier_id`, `courier_route_id`, `delivery_type_id`, `waybill_url`, `label_url`, `order_id`, `tracking_number`, `status`, `timestamp`, `retailer_id`, `package_id`, `customer_phone`, `customer_city`, `customer_email`, `customer_address`, `price`
-   **Relationships**:
    -   Belongs to `Courier`
    -   Belongs to `CourierRoute`
    -   Belongs to `DeliveryType`
    -   Belongs to `Retailer`
    -   Belongs to `Package`

this table is intentional demoralized (the customer) because the customer data is coming from outside (the retailer app), and each order may have its own address.

## Indexing Strategy for ZidShip

To optimize the performance of database queries, especially for a system like ZidShip that handles a significant volume of shipments, indexing is crucial. Indexes speed up the retrieval of rows from the database table. Here's a breakdown of the indexing strategy based on the provided queries and the database design:

### Indexes:

1. **CourierRoute Table**:

    - Columns: `origin`, `destination`.
    - Reason: The query filters `CourierRoute` based on both `origin` and `destination`. A composite index on these columns will speed up this lookup.

2. **Courier Table**:

    - Columns: `max_capacity`, `current_usage`.
    - Reason: The query filters couriers where `max_capacity` is greater than `current_usage`. Indexing these columns will optimize this comparison.

3. **Default Indexes**:
    - MySQL automatically creates an index for primary keys. This means tables like `Courier`, `CourierRoute`, `Shipment`, etc., will have indexes on their primary key columns (`id` by default in Laravel).
    - Additionally, when defining foreign key constraints in MySQL, an index on the foreign key column(s) is automatically created if it doesn't exist.

### Outcome:

With these indexes in place, the provided query will execute more efficiently. The `whereHas` clause, which checks for couriers with specific routes, will benefit from the composite index on `origin` and `destination` in the `CourierRoute` table. The comparison of `max_capacity` and `current_usage` in the `Courier` table will also be optimized with the respective indexes.

It's worth noting that while indexes speed up data retrieval, they can slightly slow down write operations (like INSERT, UPDATE, DELETE) because the index also needs to be updated. However, in a system like ZidShip, where fast data retrieval is crucial, the benefits of indexing outweigh the minor write performance overhead.
