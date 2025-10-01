

# Real-Time Collaborative Document Editor

This project is a **Laravel 12 + React based real-time document editor**.  
Multiple users can create, edit, and collaborate on documents live. It also includes features like **document versioning, invitations, search, and rate limiting**.

---

## 🚀 Setup Instructions

### 1. Clone the Repository
```bash
git clone https://github.com/Sweetysinghcdac/realtime-editor.git


cd backend
cp .env.example .env
composer install
composer install
cp .env.example .env
# edit .env DB & REVERB_*
php artisan key:generate
php artisan migrate
php artisan queue:table
php artisan migrate
php artisan install:broadcasting

# Start the Laravel server:
# start reverb (dev)
php artisan reverb:start --host=0.0.0.0 --port=6001 --debug
# start queue worker
php artisan queue:work
php artisan serve





# Backend (backend/.env)
APP_NAME="Realtime Editor"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=realtime_editor
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=reverb
QUEUE_CONNECTION=redis

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=6001



# frontend (in /editor_frontend )
#  Frontend (React) Setup
cd react_frontend
Start the development server:

Install Node.js dependencies:

npm install laravel-echo pusher-js

VITE_API_URL=http://127.0.0.1:8000
VITE_REVERB_APP_KEY=your-app-key
VITE_REVERB_HOST=127.0.0.1
VITE_REVERB_PORT=6001








<---------API Endpoints--------->

Here are the key API endpoints you’ll be using:

Authentication

POST /api/register → Register a new user
Params: name, email, password

POST /api/login → Login user
Params: email, password

Documents

GET /api/documents → Get all documents for logged-in user

POST /api/documents → Create a new document
Params: title, content

GET /api/documents/{id} → Get a specific document

PUT /api/documents/{id} → Update document
Params: title, content

DELETE /api/documents/{id} → Delete document

Document Versioning

GET /api/documents/{id}/versions → List all versions of a document

POST /api/documents/{document}/revert/{version} → Revert to a specific version

Invitations

GET /api/invitations → List all invitations

POST /api/documents/{document}/invite → Send invitation

POST /api/invitations/{id}/accept → Accept invitation

POST /api/invitations/{id}/decline → Decline invitation


POST /api/documents/{document}/revoke/{user} → Revoke invitation

Search

GET /api/search?query=keyword → Search documents by title/content






Best Practices Followed

Code follows Laravel conventions (Controllers, Models, Requests, Policies)

Authentication handled with Sanctum/JWT

WebSockets powered by Laravel Reverb + Echo

Rate limiting applied to sensitive API endpoints

Error handling done with user-friendly popups (instead of raw console errors)