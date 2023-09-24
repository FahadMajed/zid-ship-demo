### Testing Strategy for ZidShip

The testing strategy for ZidShip centers around integration tests, with a primary focus on the shipment creation process. Below is a breakdown of the approach and the rationale behind it:

#### 1. Why Choose Integration Over Unit Testing?
- **Complex Interactions**: The shipment creation process involves interactions between multiple system components. Integration tests validate these intricate interactions, ensuring cohesive functionality.
  
- **Service Layer Focus**: The strategy targets the `ShipmentService`, the core of the business logic. Integration tests validate its interactions with models, databases, and potential external services.

- **Database Realism**: Unlike unit tests that might mock operations, integration tests handle real database interactions, ensuring accurate execution during shipment creation.

- **In-Memory Database Benefits**:
  - **Speed**: In-memory databases operate faster than traditional databases.
  - **Isolation**: Tests remain separate from the production database and from each other, maintaining a consistent environment.

- **Real-World Simulation**: Tests mimic actual user scenarios, from creating multiple shipments to handling missing data, ensuring real-world reliability.

#### 2. Rationale Behind No Tests for Couriers:
Testing couriers directly, especially when involving actual calls to external systems, can be counterproductive. Such tests might be slow, fragile, and complex. Instead:
- **External System Calls**: Testing actual calls to external systems, like couriers, can lead to fragile and slow tests. Tools like Insomnia effectively test courier APIs.
  
- **Mocking Responses**: If testing becomes necessary, mocking the responses set by the courier interface ensures speed and stability.

#### 3. The Significance of Stable APIs:
Regardless of underlying implementation details, the API or Facade of a module remains crucial. In this context, the `ShipmentService` might encompass multiple inner services. However, its API remains pivotal. This approach ensures:
- **Test Stability**: APIs change less frequently than implementation details. Stable tests focus on these APIs, ensuring longevity and reduced maintenance.
  
- **Flexibility in Implementation**: With tests targeting the API, the underlying implementation can evolve without the need for constant test rewrites.
