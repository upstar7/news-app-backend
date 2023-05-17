## Server-side Setup

1. clone / download the project folder `git clone https://github.com/DarshPhpDev/news-aggregator.git`

2. Navigate to the backend directory: `cd news-aggregator/backend`

3. Run the following command: `docker-compose up -d`

4. Run the migrate command `docker-compose exec backend-app ./artisan migrate --seed`

5. Run the news scrapping command manually `docker-compose exec backend-app ./artisan news:fetch` or add the following command to your server crontab setup `* * * * * php /path/to/backend/folder/artisan schedule:run >> /dev/null 2>&1`

6. Open your browser and navigate to: `http://localhost:8000`... finally the backend server is up and running
