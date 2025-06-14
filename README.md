# Goat Management System 🐐

A comprehensive Laravel-based management system for goat farming operations, built with Filament PHP for an intuitive admin interface. This system helps farmers manage their livestock, track breeding, monitor health, manage finances, and generate detailed reports.

## Features

### 🐐 Livestock Management

-   **Goat Registration**: Complete goat profiles with tag numbers, breeds, gender, birth dates
-   **Photo Management**: Upload and manage multiple photos per goat
-   **Breeding & Fattening Categories**: Separate management for breeding stock and fattening goats
-   **QR Code Generation**: Generate QR codes for each goat for easy identification
-   **Genealogy Tracking**: Track parent-offspring relationships

### 🔄 Breeding Management

-   **Mating Records**: Track breeding activities with expected delivery dates
-   **Pregnancy Monitoring**: Monitor pregnancy status and health notes
-   **Delivery Management**: Record births with detailed offspring information
-   **Automatic Calculations**: Auto-calculate delivery dates (150-day gestation period)

### 📊 Health & Nutrition

-   **Health Records**: Complete medical history with diagnoses, treatments, and medications
-   **Feeding Logs**: Track feed types, quantities, and costs (individual or group feeding)
-   **Weight Tracking**: Monitor weight changes over time with visual indicators
-   **Veterinary Management**: Record veterinarian visits and next checkup reminders

### 💰 Financial Management

-   **Sales Tracking**: Record sales with automatic profit calculations
-   **Expense Management**: Categorized expense tracking (feed, medical, equipment, etc.)
-   **Cost Analysis**: Track costs per goat including feed and medical expenses
-   **Profit Analysis**: Detailed profit calculations for fattening operations

### 📈 Analytics & Reporting

-   **Dashboard Widgets**: Real-time statistics and key metrics
-   **Revenue Charts**: Monthly revenue and profit visualization
-   **Expense Breakdown**: Visual expense categorization
-   **Breeding Statistics**: Breeding performance metrics
-   **Growth Charts**: Individual goat weight progression
-   **Custom Reports**: Financial, livestock, breeding, and health reports

### 🔔 Automated Reminders

-   **Delivery Alerts**: Upcoming delivery notifications (next 7 days)
-   **Health Checkups**: Overdue health checkup reminders
-   **Daily Scheduling**: Automated daily reminder system

## Tech Stack

-   **Backend**: Laravel 10.x
-   **Admin Panel**: Filament PHP 3.x
-   **Database**: MySQL/PostgreSQL
-   **Frontend**: Blade Templates with Tailwind CSS
-   **Media Management**: Spatie Media Library
-   **QR Codes**: Simple QR Code Generator
-   **Scheduling**: Laravel Task Scheduling
-   **Localization**: Indonesian (Bahasa Indonesia) support

## Installation

### Prerequisites

-   PHP 8.1+
-   Composer
-   Node.js & NPM
-   MySQL/PostgreSQL database

### Setup Steps

1. **Clone the repository**

    ```bash
    git clone https://github.com/yourusername/goat-management-system.git
    cd goat-management-system
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Install Node dependencies**

    ```bash
    npm install
    ```

4. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. **Configure database**
   Edit `.env` file with your database credentials:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=goat_management
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

6. **Run migrations**

    ```bash
    php artisan migrate
    ```

7. **Create storage link**

    ```bash
    php artisan storage:link
    ```

8. **Create admin user**

    ```bash
    php artisan make:filament-user
    ```

9. **Build assets**

    ```bash
    npm run build
    ```

10. **Start the server**

    ```bash
    php artisan serve
    ```

11. **Setup task scheduling** (for automated reminders)
    Add to your crontab:
    ```bash
    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
    ```

## Usage

### Accessing the System

-   Navigate to `http://localhost:8000/admin`
-   Login with your admin credentials
-   The dashboard will show key metrics and upcoming alerts

### Managing Goats

1. **Add New Goat**: Go to Goats → Create
2. **Upload Photos**: Use the photo upload section in goat forms
3. **Generate QR Code**: Click "QR Code" action in goat list
4. **Track Weight**: Use the Weight Logs section

### Recording Breeding

1. **Mating Record**: Create mating records with male/female pairs
2. **Pregnancy**: Auto-created from successful matings
3. **Delivery**: Record births through pregnancy records

### Financial Tracking

1. **Sales**: Record when goats are sold with automatic profit calculation
2. **Expenses**: Categorize all farm expenses
3. **Reports**: Generate financial summaries by date range

### Health Management

1. **Health Records**: Record medical treatments and diagnoses
2. **Feeding Logs**: Track daily feeding (individual or group)
3. **Reminders**: System automatically tracks overdue checkups

## Configuration

### Localization

The system supports Indonesian localization. Key files:

-   `config/app.php`: Set locale to 'id'
-   `resources/lang/id/`: Indonesian translations

### Currency

Indonesian Rupiah (IDR) formatting is handled by:

-   `app/Helpers/CurrencyHelper.php`
-   Automatic formatting in forms and displays

### Breeding Settings

-   Default gestation period: 150 days (configurable in `MatingRecord` model)
-   Reminder timeframes can be adjusted in `SendReminders` command

## Database Schema

### Key Models

-   **Goat**: Main livestock entity
-   **MatingRecord**: Breeding activities
-   **Pregnancy**: Pregnancy tracking
-   **KiddingRecord**: Birth records
-   **Sale**: Sales transactions
-   **Expense**: Farm expenses
-   **HealthRecord**: Medical records
-   **WeightLog**: Weight measurements
-   **FeedingLog**: Feeding activities

### Relationships

-   Goats have parents (father/mother relationships)
-   Pregnancies link to mating records
-   Sales link to specific goats with profit calculations
-   Health and feeding logs belong to goats

## API Endpoints

The system includes basic API routes for potential mobile app integration:

-   `/api/user` - Authenticated user info (with Sanctum)

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines

-   Follow PSR-12 coding standards
-   Write tests for new features
-   Update documentation for new functionality
-   Use meaningful commit messages

## Screenshots

### Dashboard

![Dashboard](screenshots/dashboard.png)

### Goat Management

![Goat List](screenshots/goat-list.png)

### Financial Reports

![Reports](screenshots/reports.png)

_Note: Add actual screenshots to a `screenshots/` directory_

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support and questions:

-   Create an issue on GitHub
-   Email: [your-email@example.com]

## Acknowledgments

-   Built with [Laravel](https://laravel.com/)
-   Admin interface by [Filament PHP](https://filamentphp.com/)
-   Icons by [Heroicons](https://heroicons.com/)
-   Media management by [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary)

## Roadmap

### Upcoming Features

-   [ ] Mobile app (React Native/Flutter)
-   [ ] SMS/WhatsApp reminders
-   [ ] Advanced reporting with charts
-   [ ] Inventory management for feed/medicine
-   [ ] Multi-farm support
-   [ ] Export data to Excel/PDF
-   [ ] Integration with veterinary services
-   [ ] Weather data integration

### Version History

-   **v1.0.0** - Initial release with core features
-   **v1.1.0** - Added breeding management
-   **v1.2.0** - Financial tracking improvements
-   **v1.3.0** - Automated reminders system

---

Made with ❤️ for goat farmers everywhere
