FROM python:3.10-slim

WORKDIR /app
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

ENV PYTHONUNBUFFERED=1

# start app - docker runs this command
CMD ["watchmedo", "auto-restart", "--patterns=*.py", "--recursive", "--", "python", "main.py"]
