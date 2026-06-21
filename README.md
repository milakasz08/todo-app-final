Projekt zaliczeniowy

MediaStream — System wypożyczeń zasobów

Aplikacja webowa stworzona w Symfony, umożliwiająca zarządzanie zasobami (książki, filmy, płyty) oraz ich wypożyczaniem przez użytkowników. System obsługuje role administratora i zwykłego użytkownika.

Wymagania:

- Docker oraz Docker Compose
- Git

Instalacja

1. Sklonuj repozytorium

git clone https://github.com/milakasz08/todo-app-final.git
cd todo-app-final

2. Uruchom kontenery Docker

docker-compose up -d

3. Wejdź do kontenera PHP

docker-compose exec php bash
cd app

4. Zainstaluj zależności PHP

composer install --no-interaction

5. Skonfiguruj zmienne środowiskowe

Skopiuj plik `.env` do `.env.local` i ustaw poprawny connection string do bazy danych, jeśli różni się od domyślnego:

cp .env .env.local

Sprawdź wartość `DATABASE_URL` w `.env.local` i dopasuj ją do swojej konfiguracji Docker Compose (host, port, nazwa bazy, użytkownik, hasło).

6. Wykonaj migracje bazy danych

php bin/console doctrine:migrations:migrate --no-interaction

7. Załaduj dane testowe

php bin/console doctrine:fixtures:load --no-interaction

8. Otwórz aplikację w przeglądarce

https://wierzba.wzks.uj.edu.pl/~24_kaszowska/todo-app/
Localhost:8000



Przykładowe konta

Administrator | admin1@gmail.com  |haslo123    
Użytkownik    | user1@gmail.com     | haslo1234   

Funkcjonalności

- Rejestracja i logowanie użytkowników
- Przeglądanie dostępnych zasobów (książki, filmy, płyty) z filtrowaniem po typie
- Składanie wniosków o wypożyczenie zasobu
- Panel administratora:
  - zarządzanie zasobami (dodawanie, edycja, usuwanie)
  - zarządzanie kategoriami
  - zatwierdzanie/odrzucanie wniosków o wypożyczenie
  - zarządzanie uprawnieniami użytkowników (nadawanie/odbieranie roli administratora)
- Dashboard z podsumowaniem statystyk (liczba zasobów, aktywne wypożyczenia, najpopularniejszy zasób)

Struktura projektu


src/
├── Controller/       Kontrolery obsługujące żądania HTTP
├── Entity/              Encje Doctrine (Category, Rental, Resource, Tag, User)
├── Form/               Formularze Symfony
├── Repository/      Repozytoria Doctrine
├── Security/          Logika uwierzytelniania (AppAuthenticator, EmailVerifier)
└── DataFixtures/   Dane testowe (AppFixtures)
