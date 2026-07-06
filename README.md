This project is a distributed system for processing and enhancing developer notes into production-ready, structured documentation. The objective is to help provide a more insightful documenttion writting using a HuggingFace Text mode. 

The system leverages **Laravel** for the frontend with **MySQL** for data management, **Python** for backend processing (with **Redis** as the message queue), **HuggingFace** transformer for AI model and **Docker** for containerization.

# Prerequisites
1. Docker and Docker Compose:
Ensure that Docker and Docker Compose are installed on your system. You can install Docker from the official website.

2. Python 3.10+
Ensure that Python 3.10 or higher is installed on your system for compatibility with dependencies.

### Tips
- Install npm & composer globally (not in container)

# Getting Started
### Clone the Repository
```bash
git clone https://github.com/iallfem13/wiki-summarizer.git
cd wiki-summarizer
```

### Set Up Environment Variables
Edit the **env** and **docker-compose.yml** files to set up your specific configuration, including Redis and database settings.

### Environment Configuration
Ensure that your **.env** files are correctly configured with the appropriate values for Redis, Laravel, and Python:

**Project Root**
```bash
cp .env.example .env
```

**Python Backend Configuration**
```bash
cp python-service/.env.example python-service/.env
```

**Laravel**
```bash
cp laravel/.env.example laravel/.env
```

### Build and Run Docker Containers
```bash
docker-compose build
docker-compose up -d
```
  
`Note`: You can see an architecture diagram under Docs/

# License
This project is licensed under the MIT License. Feel free to use, modify, and distribute it as needed.