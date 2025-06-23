# Truthfulness Stance Annotation Tool

A web-based platform for collecting human annotations to train machine learning models for measuring truthfulness stance detection between social media posts (tweets) and factual claims.

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Architecture](#architecture)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Database Schema](#database-schema)
- [API Endpoints](#api-endpoints)
- [Annotation Process](#annotation-process)
- [Quality Control](#quality-control)
- [Project Structure](#project-structure)
- [Dependencies](#dependencies)
- [Contributing](#contributing)
- [License](#license)

## 🎯 Overview

This platform enables researchers to collect high-quality human annotations for stance detection tasks. The system presents pairs of factual claims (from PolitiFact) and tweets (from Twitter) to annotators who determine the truthfulness stance expressed by the tweet toward the claim.

### Research Purpose
- Train machine learning models for automated stance detection
- Understand how social media users express stance toward factual claims
- Support fact-checking research and misinformation detection

### Annotation Categories
The tool supports five stance categories:
- **Negative (-1)**: The tweet believes the factual claim is false
- **Neutral (0)**: The tweet expresses neutral or no stance toward the claim's truthfulness
- **Positive (1)**: The tweet believes the factual claim is true
- **Different Topic (2)**: The tweet and claim discuss different topics
- **Problematic (3)**: The tweet is sarcastic/parody or has technical issues (broken links, paywall content)
- **Skip (-2)**: Option to skip difficult pairs

## ✨ Features

### Core Functionality
- **User Authentication**: Secure login/registration system with password reset
- **Training Mode**: 16 training questions with immediate feedback and explanations
- **Annotation Interface**: Clean, intuitive interface for stance annotation
- **Progress Tracking**: Real-time progress monitoring and statistics
- **Quality Assessment**: Work quality scoring based on gold standard pairs
- **Leaderboard**: Gamified ranking system to encourage participation
- **Previous Responses**: Ability to review and modify previous annotations

### Quality Control
- **Gold Standard Pairs**: Expert-annotated pairs for quality assessment
- **Top Participant Filtering**: Quality-based participant screening
- **Agreement Metrics**: Inter-annotator agreement tracking
- **Screening Questions**: Quality control mechanisms

### Administrative Features
- **Progress Dashboard**: Comprehensive statistics and progress monitoring
- **User Management**: Track participant performance and activity
- **Data Export**: Export annotations for analysis
- **Feedback System**: Built-in feedback collection mechanism

## 🏗️ Architecture

### Technology Stack
- **Backend**: PHP with PDO for database operations
- **Frontend**: HTML5, CSS3, JavaScript (jQuery)
- **Database**: MySQL
- **UI Framework**: Bootstrap 3.3.2
- **Email**: PHPMailer for password reset functionality
- **Additional Libraries**: iCheck for enhanced form controls

### Key Components
- **Database Layer** (`db.php`): PDO-based database abstraction
- **Authentication System**: Session-based user management
- **Annotation Engine**: Core logic for serving and collecting annotations
- **Quality Control System**: Automatic quality scoring and participant filtering
- **Progress Tracking**: Real-time statistics and leaderboard generation

## 🚀 Installation

### Prerequisites
- PHP 7.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (for PHP dependencies)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd stancedatacollection
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies** (for development tools)
   ```bash
   npm install
   ```

4. **Database Setup**
   - Create a MySQL database
   - Import the database schema (see `mysql_queries.txt` for triggers)
   - Configure database credentials

5. **Configuration**
   - Copy and configure `credential.php` with your database credentials
   - Update `GLOBAL.php` for environment settings
   - Set up email configuration for password reset functionality

6. **Web Server Configuration**
   - Point document root to the project directory
   - Ensure PHP has write permissions for session management

## ⚙️ Configuration

### Environment Configuration (`GLOBAL.php`)
```php
<?php
$LOCAL = false;        // Set to true for local development
$GROUNDTRUTH_ENV = false;  // Set to true for ground truth environment
?>
```

### Database Configuration (`credential.php`)
```php
<?php
$DB_PASSWORD = "your_database_password";
?>
```

### Index File Variants
The project includes three main entry points:
- `index.php`: Standard annotation interface
- `index_with_payment.php`: Interface with payment tracking
- `index_with_score.php`: Interface with enhanced scoring features

## 📖 Usage

### For Administrators

1. **Setup Database**: Initialize MySQL database with required tables
2. **Configure Environment**: Set up `GLOBAL.php` and `credential.php`
3. **Load Data**: Import factual claims and tweets into the database
4. **Monitor Progress**: Use `get_progress.php` for real-time monitoring

### For Annotators

1. **Registration**: Create an account with email verification
2. **Training Phase**: Complete 16 training questions with feedback
3. **Annotation Phase**: Annotate claim-tweet pairs
4. **Quality Tracking**: Monitor work quality through the leaderboard
5. **Review/Modify**: Use "Previous Responses" to review and update annotations

### Key Pages
- `/index.php`: Main annotation interface
- `/get_progress.php`: Progress monitoring dashboard
- `/datathon_leaderboard.php`: Public leaderboard
- `/get_leaderboard.php`: Detailed leaderboard with statistics

## 🗄️ Database Schema

### Core Tables
- **Sentence**: Stores claim-tweet pairs with metadata
- **Sentence_User**: Stores user annotations and responses
- **User**: User account information and statistics
- **Training**: Training question configurations
- **User_Training**: Tracks user progress through training
- **Activity**: Logs all user interactions

### Key Fields
- **Sentence Table**: `id`, `claim`, `tweet`, `tweet_id`, `claim_verdict`, `screening`, `subset`
- **Sentence_User Table**: `sentence_id`, `username`, `response`, `time`, `context_seen`
- **User Table**: `username`, `email`, `password`, `university`, `consent`

## 🔌 API Endpoints

### Authentication
- `check_sign_in_information.php`: User login validation
- `insert_new_user.php`: New user registration
- `verify_user.php`: Email verification
- `reset_password.php`: Password reset functionality

### Annotation
- `get_sentence.php`: Retrieve next annotation pair
- `set_response.php`: Submit annotation response
- `get_previous_answers.php`: Retrieve user's previous annotations
- `change_response.php`: Modify existing annotations

### Progress & Statistics
- `get_progress.php`: Comprehensive progress dashboard
- `get_leaderboard.php`: Leaderboard data
- `get_answer_count.php`: User annotation count

### Training
- `get_training_index.php`: Get next training question
- `set_training_index.php`: Process training responses

## 📝 Annotation Process

### Training Phase (16 Questions)
1. Users complete training questions with immediate feedback
2. Correct answers and explanations are provided
3. System tracks training performance
4. Transition to main annotation after completion

### Main Annotation Phase
1. **Pair Selection**: Algorithm selects appropriate claim-tweet pairs
2. **Annotation**: User selects stance category
3. **Quality Check**: Response compared against gold standards
4. **Progress Update**: Statistics and leaderboard updated
5. **Next Pair**: System serves next appropriate pair

### Quality Control Mechanisms
- **Gold Standard Comparison**: Responses compared to expert annotations
- **Top Participant Filtering**: High-quality annotators identified
- **Agreement Thresholds**: Minimum agreement requirements
- **Screening Questions**: Quality control pairs interspersed

## 🎯 Quality Control

### Quality Scoring Algorithm
The system implements a sophisticated quality scoring mechanism:

```
Quality Score = -0.20 * (Perfect Matches) + 
                0.50 * (Adjacent Category Errors) + 
                0.50 * (Moderate Errors) + 
                1.00 * (Major Errors) + 
                2.00 * (Severe Errors)
```

### Top Participant Identification
- Minimum 50 annotations required
- Quality score ≤ 0.0 threshold
- Automatic filtering for high-quality annotations

### Consensus Mechanisms
- Multiple annotations per pair
- Majority voting for final labels
- Agreement threshold requirements
- Quality-weighted consensus

## 📁 Project Structure

```
stancedatacollection/
├── index.php                 # Main annotation interface
├── index_with_payment.php    # Payment-enabled interface  
├── index_with_score.php      # Score-enhanced interface
├── db.php                    # Database abstraction layer
├── GLOBAL.php               # Environment configuration
├── credential.php           # Database credentials
├── vars.php                 # Global variables and queries
│
├── Authentication/
│   ├── check_sign_in_information.php
│   ├── insert_new_user.php
│   ├── verify_user.php
│   ├── reset_password.php
│   └── forgot_password.php
│
├── Annotation/
│   ├── get_sentence.php
│   ├── set_response.php
│   ├── change_response.php
│   └── get_previous_answers.php
│
├── Training/
│   ├── get_training_index.php
│   └── set_training_index.php
│
├── Progress/
│   ├── get_progress.php
│   ├── get_leaderboard.php
│   ├── get_answer_count.php
│   └── datathon_leaderboard.php
│
├── Utilities/
│   ├── get_consent.php
│   ├── set_consent.php
│   ├── send_feedback.php
│   └── clear_session.php
│
├── Frontend/
│   ├── css/
│   │   └── index.css
│   ├── js/
│   │   ├── index.js
│   │   ├── index_w_payment.js
│   │   └── index_wo_payment.js
│   ├── bootstrap-3.3.2-dist/
│   ├── iCheck/
│   └── image/
│
├── Scripts/
│   ├── gold_label_statistic.py
│   └── Result_19.csv
│
└── Documentation/
    ├── README.md
    ├── LICENSE
    └── mysql_queries.txt
```

## 📦 Dependencies

### PHP Dependencies (composer.json)
- **phpmailer/phpmailer**: Email functionality for password reset

### JavaScript Dependencies (package.json)
- **@prettier/plugin-php**: Code formatting for PHP
- **prettier**: Code formatter

### Frontend Libraries
- **Bootstrap 3.3.2**: Responsive UI framework
- **jQuery**: JavaScript library for DOM manipulation
- **iCheck**: Enhanced checkbox and radio button styling

## 🤝 Contributing

### Development Setup
1. Clone the repository
2. Install dependencies: `composer install && npm install`
3. Configure local environment
4. Set up local database
5. Test functionality

### Code Style
- PHP: Follow PSR-12 coding standards
- JavaScript: Use Prettier for formatting
- Database: Follow MySQL naming conventions

### Testing
- Test all annotation workflows
- Verify quality control mechanisms
- Check responsive design
- Validate email functionality

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

**Copyright (c) 2021 朱正源**

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

---

## 📞 Contact & Support

For questions, issues, or contributions, please contact the research team at **idirlab@uta.edu**.

### Research Context
This tool was developed as part of stance detection research to support fact-checking and misinformation detection efforts. The collected annotations will be used to train machine learning models for automated stance detection in social media content.
