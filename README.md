# Kartotēka

Employee management application built with Symfony and Vue.js

It allows to manage Employees and their data such as personal information and previous employment history.

## Implemented Features:
- List all Employees
- Show detailed Employee information
- Create new Employee with Previous Employments
- Update Employee with Previous Employments
- Delete Employee with Previous Employments
- Print Employee details

## Tech Stack:
### backend
- PHP 8.4
- Symfony 8.1 
- Doctrine ORM 
- PostgreSQL 18 
- PHPUnit

### frontend
- Vue 3
- TypeScript
- Vite
- Vue Router

### Environment
- Docker Compose

## Requirements
- Docker with Docker Compose
- Make

## Getting started

Clone the repository and navigate to the directory

Run:
```
make setup
```

It should:
- Build the Docker images;
- Install backend and frontend dependencies;
- Start the services;
- Prepare the development and test databases and run migrations.

Once successful, head to: http://localhost:5173, where the application is ready to be used.

### Additional commands
- `make up` - Start the application;
- `make down` - Stop the application;
- `make enter-backend-container` - Enter backend container;
- `make test` - Run the backend tests;

## Running tests
Run the backend tests using:
```
make test
```

This application's domain and application logic is covered with Unit tests and Doctrine repository integration tests.


## Architecture
The backend is built following main principles of Domain-Driven Design (DDD) and Hexagonal Architecture. Not all the principles are implemented as some of them wouldn't bring any benefit at this scale.
Following the principles, 3 layers were created:
- Domain - Contains the employee aggregate, entities, value objects, business rules, and repository interfaces;
- Application - Contains use cases that coordinate domain operations to achieve specific goals;
- Adapters - Connect the application to external systems, including HTTP controllers and the Doctrine repository implementation.

The frontend is a Vue single page application that communicates with the backend through REST API.