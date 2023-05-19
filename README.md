## News APP Backend

1. git clone https://github.com/harrisrui/news-app-backend.git

2. Run this command: `docker-compose up -d`

3. Run this migrate command `docker-compose exec backend-app ./artisan migrate --seed`

4. Run this command `docker-compose exec backend-app ./artisan news:fetch`

5. Open your browser and navigate to: `http://localhost:8000`... finally the backend server is up and running
