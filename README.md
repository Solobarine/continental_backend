<img src='./public/logo.svg' alt='Logo' width='80'/>
# Continental

## Table of Contents
- [Introduction](#introduction)
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Front End](#🌍-front-end)
- [Getting Started](#getting-started)
  - [Clone the Repository](#clone-the-repository)
  - [Install Dependencies](#install-dependencies)
  - [Configure Environment Variables](#configure-environment-variables)
  - [Run the Application](#run-the-application)
  - [Access the API](#access-the-api)
- [Author](#👤-author)
- [Contributing](#contributing)
- [Issues and Questions](#issues--questions)
- [Show your Support](#🙏-show-your-support)
- [License](#📝-license)

## Introduction
Welcome to the **Continental** backend application! Continental is the backend system of the Continental web app – a financial application designed for users to conveniently send and receive money. This backend system handles various functionalities to ensure a secure and seamless user experience.

## Features
### 1. Form Validation
Continental employs robust form validation mechanisms to ensure that user inputs are accurate, complete, and meet the required criteria. This prevents erroneous data from entering the system, leading to a more reliable and error-free experience.

### 2. JWT Authentication
User security is a top priority for Continental. We use JSON Web Tokens (JWT) to implement a strong authentication process. This ensures that only authorized users can access the application and its features, safeguarding sensitive financial information.

### 3. Deposit and Withdrawal
Users can easily deposit and withdraw funds through the Continental web app. The backend handles these transactions securely, updating user account balances accurately and promptly.

### 4. Transaction Messages
Upon completing a transaction, users receive clear and concise messages indicating whether the transaction was successful or unsuccessful. This helps users stay informed about the status of their financial activities.

### 5. Login Messages
When users successfully log in to their accounts, they receive a welcoming message. This friendly touch enhances the user experience and assures them that their login was successful.

### 6. Database Integration
Continental employs a robust database system to store user account information, transaction history, and other critical data. This ensures data integrity, scalability, and efficient data retrieval for a seamless user experience.

### 7. User Profile Management
Users can create and edit their profiles through the web app. This feature allows them to keep their personal and financial information up to date and relevant.

## Technologies Used
- **PHP**: 
PHP is a widely-used server-side scripting language primarily designed for web development
- **Laravel**: Laravel is a popular open-source PHP web framework known for its elegant syntax and robust features that streamline and simplify web application development.
- **JSON Web Token(JWT)**: JWT (JSON Web Token) is a compact and self-contained method for securely transmitting information between parties as a JSON object.
- **MySQL**: 
MySQL is an open-source relational database management system that efficiently stores and manages structured data for various applications.

## 🌍 Front End

The Project also has a Front End. Click <a href='https://github.com/solobarine/continental'>here</a> to view the Front End.

## Getting Started
To set up the Continental backend app locally, follow these steps:

### Clone the Repository
```shell
git clone https://github.com/solobarine/continental_backend.git
```

### Install Dependencies
```shell
cd continental-backend
composer install
```

### Configure Environment Variables
- Create a .env file based on the provided .env.example template.
- Configure your database connection settings, JWT secret key, and any other required variables.

### Run the Application
```shell
php artisan serve
```

### Access the API
The API will be available at http://localhost:8000. You can use tools like Postman to interact with the API endpoints.

## 👤 Author

- Name: **Solomon Barine Akpuru**
- GitHub: [@solobarine](https://github.com/solobarine)
- LinkedIn: [solomon-akpuru](https://www.linkedin.com/in/solomon-akpuru)

## Contributing
We welcome contributions to the Continental backend app! If you'd like to contribute, please fork the repository, make your changes, and submit a pull request. Be sure to follow our contribution guidelines.

## Issues & Questions
If you encounter any issues, have questions, or need assistance, please open an issue. We're here to help!

## 🙏 Show your Support

Give a ⭐️ if you like the project!

## 📝 License

Go Buddy is released under the [MIT](./LICENSE) License.

Thank you for choosing Continental. Together, we're creating a secure and convenient financial experience for users around the world.