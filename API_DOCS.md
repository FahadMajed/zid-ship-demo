## ZidShip API Documentation

### 1. Create Bulk Shipment

- **Endpoint**: `/shipments/bulk`
- **Method**: `POST`
- **Description**: Allows retailers to create multiple shipments at once.

#### Request Body:

```json
{
"retailer_name": "Retailer Name",
"shipments": [
{
"customer": {
"name": "Customer Name",
"phone": "Customer Phone",
"email": "Customer Email",
"city": "Customer City"
},
"package": {
"height": "Package Height",
"width": "Package Width",
"length": "Package Length",
"weight": "Package Weight",
"description": "Package Description (optional)"
},
"order_id": "Order ID in the retailer app",
"delivery_type": "Prime OR Fast OR Usual"
}
// ... other shipments
]
}
```

#### Response:
- **HTTP 202 (Accepted)**:

```json
{
"shipments": [
{
"data": {
"shipment_id": "Shipment ID",
"status": "Shipment Status (Pending, Confirmed, In Transit, Picked Up, Out For Delivery, Delivered, Exception, Cancelled)"
},
"error": "Error Message (if any)"
}
// ... other shipment responses
]
}
```
### 2. Handle Shipment Events (Webhook Callback)

- **Endpoint**: `/shipments/{shipment_id}/events`
- **Method**: `POST`
- **Description**: Webhook endpoint for couriers to send events and update shipment statuses.

#### Path Parameters:
- `shipment_id`: ID of the shipment for which the event is being sent.

#### Request Body:
```json
{
"shipment_id": "Shipment ID",
"order_id": "Order ID",
"courier_name": "courier name"
}
```

#### Response:
- **HTTP 200 (OK)**:
```json
{
"status": "success"
}
```

### 3. Get Shipment Status

- **Endpoint**: `/shipments/{shipment_id}/track`
- **Method**: `GET`
- **Description**: Retrieve the status of a specific shipment.

#### Path Parameters:
- `shipment_id`: ID of the shipment to track.

#### Response:
- **HTTP 200 (OK)**:

```json
{
"status": "Shipment Status"
}
```

### 4. Get Shipment Details

- **Endpoint**: `/shipments/{shipment_id}`
- **Method**: `GET`
- **Description**: Retrieve details of a specific shipment, including waybill and label URLs.

#### Path Parameters:
- `shipment_id`: ID of the shipment to retrieve.

#### Response:
- **HTTP 200 (OK)**:
```json
{
"waybill_url": "Waybill URL",
"label_url": "Label URL"
}
```
