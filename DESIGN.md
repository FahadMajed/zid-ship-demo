## Design Decisions and Trade-offs

Given the context of ZidShip, which I assume, handles a decent volume of shipments (approximately 1000 daily), the design decisions were made with scalability, reliability, and data integrity in mind.

### Relational Database

**Decision**: The ZidShip system is built upon a relational database.

**Rationale**:

- **Structured Data & Relationships**: ZidShip deals with entities like shipments, retailers, couriers, and packages. These entities not only have defined attributes but also inherent relationships. For instance, a shipment belongs to a retailer, is handled by a courier, and contains packages. An RDBMS, with its table structures and foreign key constraints, naturally represents and enforces these relationships, ensuring data consistency.

- **ACID Properties**: In a system like ZidShip, ensuring that every transaction is processed reliably is paramount. RDBMS offers ACID (Atomicity, Consistency, Isolation, Durability) properties. This means, for example, if a shipment is created, all associated data changes (like updating courier capacity or billing the retailer) either complete successfully together or none of them do, preventing data anomalies.

- **Complex Queries**: As the system grows, there might be a need for complex queries, like "Find all shipments from a particular retailer, handled by a specific courier, that were delayed." RDBMSs excel in such complex, multi-table queries, providing fast and consistent results.

### Why not NoSQL (e.g., MongoDB)?

While NoSQL databases like MongoDB offer flexibility in data modeling and can handle large volumes of data, there are specific reasons why it might not be the best fit for ZidShip:

- **Lack of Strict Relationships**: MongoDB is schema-less. While this provides flexibility, it can lead to data inconsistency in a system like ZidShip where relationships between entities are crucial. For instance, ensuring that every shipment is linked to a valid retailer or courier is naturally enforced in an RDBMS through foreign key constraints. In MongoDB, such enforcement would be application-dependent, increasing the risk of errors.

- **Transactions**: Recent versions of MongoDB support multi-document transactions, but they aren't as mature or as integral to the system as the ACID properties in RDBMS. Given the critical nature of shipment data, the robust transactional guarantees of RDBMS are a significant advantage.

- **Data Normalization**: In MongoDB, there's a tendency to denormalize data (e.g., embedding documents). While this can speed up some queries, it can also lead to data redundancy and potential update anomalies. In contrast, RDBMS promotes data normalization, reducing redundancy

Although I did not utlize the transactions in the demo, but that what would happen in the real world

### Laravel's Built-in Queue System

**Decision**: For the handling bulk shipments, Laravel's built-in database-driven queue system was employed for asynchronous shipment processing.

**Rationale**:

- **Asynchronous Processing**: Handling shipments, especially in significant volumes, requires a system that can process tasks in the background, ensuring that user-facing operations remain snappy and responsive. Laravel's queue system allows for this deferred execution, improving overall system performance.

- **Integration with Laravel**: Utilizing Laravel's native tools ensures seamless integration, reducing potential integration bugs and overhead. This means that the system can leverage all the benefits of Laravel's ecosystem, including its robust error handling and retry mechanisms for failed jobs.

- **Reliability**: The database-driven queue ensures that if a job fails, it remains in the queue for retry. This is crucial for operations like shipment processing, where missing or delaying a task can have significant implications.

**Trade-offs**:

- **Performance Overhead**: Using a database as a queue can introduce performance overhead, especially as the number of jobs grows. This might necessitate regular maintenance or pruning of the jobs table to ensure optimal performance.

- **Scalability Concerns**: While suitable for the current volume, as the system scales, there might be a need to consider more scalable queue drivers like Redis or dedicated queue systems.

### Message Brokers (e.g., Apache Kafka)

**Decision**: While not implemented in the demo, for a real-world application of ZidShip, introducing a message broker like Apache Kafka is highly recommended.

**Rationale**:

- **Event Broadcasting**: As ZidShip is part of Zid products, and integrates with other systems or modules (like notifications, analytics, orders), there's a need for efficient and reliable inter-service communication. A message broker ensures that events, especially those related to shipments, are broadcasted and consumed efficiently across systems.

- **Decoupling**: Using a message broker decouples the core ZidShip system from other modules or services. This architectural decision ensures that changes or failures in one system won't directly impact others, leading to a more resilient and maintainable system.

- **Scalability**: Brokers like Kafka are designed to handle massive volumes of data, ensuring that as ZidShip scales, event broadcasting remains efficient and reliable.

**Trade-offs**:

- **Complexity**: Introducing a message broker adds another layer of complexity to the system. It requires additional setup, maintenance, and monitoring.

- **Latency**: While message brokers are designed for high throughput, they can introduce a slight latency in message delivery, especially during peak loads.

### NoSQL for Logging

**Decision**: Although not implemented in the demo, using a NoSQL database like MongoDB for logging is a consideration for the real-world application of ZidShip.

**Rationale**:

- **Flexible Schema**: Logging can involve varied data structures, and over time, the data maybe unexpected. NoSQL databases, with their schema-less nature, can accommodate this variability without requiring schema migrations.

- **High Write Throughput**: Logging systems typically have a high write rate. NoSQL databases are optimized for high write throughput, ensuring that log entries are recorded swiftly without causing system bottlenecks.

- **Scalability**: NoSQL databases can scale horizontally, making them suitable for logging systems that might accumulate vast amounts of data over time.

- **Reasonable Queries**: Databases like Mongo DB offers a decent quering system which will allow analyzing the logs with ease and to make it easier to sift through logs, identify patterns, or diagnose issues.

