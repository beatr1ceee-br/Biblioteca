document.addEventListener("DOMContentLoaded", function () {
    const body = document.body;
    const themeToggle = document.getElementById("themeToggle");

    const savedTheme = localStorage.getItem("theme") || "light";

    if (savedTheme === "dark") {
        body.classList.add("dark-theme");

        if (themeToggle) {
            themeToggle.textContent = "☀️";
        }
    } else {
        if (themeToggle) {
            themeToggle.textContent = "🌙";
        }
    }

    if (themeToggle) {
        themeToggle.addEventListener("click", function () {
            body.classList.toggle("dark-theme");

            if (body.classList.contains("dark-theme")) {
                localStorage.setItem("theme", "dark");
                themeToggle.textContent = "☀️";
            } else {
                localStorage.setItem("theme", "light");
                themeToggle.textContent = "🌙";
            }
        });
    }

    const translations = {
        ro: {
            "nav.home": "Acasă",
            "nav.books": "Cărți",
            "nav.categories": "Categorii",
            "nav.about": "Despre",
            "nav.contact": "Contact",

            "auth.login": "Autentificare",
            "auth.member": "Devino membru",
            "auth.hello": "Salut",
            "auth.logout": "Logout",
            "auth.dashboard": "Dashboard",

            "hero.title": "Bine ai venit la Biblioteca Online",
            "hero.text": "Descoperă cărți extraordinare, cunoaștere valoroasă și bucuria lecturii în fiecare zi.",
            "hero.explore": "Explorează cărțile",
            "hero.member": "Devino membru",
            "hero.myPage": "Pagina mea",

            "search.label": "Găsește cartea potrivită pentru tine",
            "search.button": "Caută",

            "section.recommended": "Cărți recomandate",
            "section.forYou": "Recomandările noastre pentru tine",
            "section.categories": "Categorii populare",
            "view.all": "Vezi toate →",

            "member.title": "Devino membru",
            "member.text": "Alătură-te comunității noastre de cititori și bucură-te de beneficii exclusive:",
            "member.li1": "✓ Acces la mii de cărți online",
            "member.li2": "✓ Reduceri și oferte speciale",
            "member.li3": "✓ Recomandări personalizate",
            "member.join": "Înscrie-te acum",

            "books.title": "Catalogul de cărți",
            "books.text": "Aici poți vedea toate cărțile disponibile în Biblioteca Online. Caută după titlu, autor sau categorie și accesează detaliile fiecărei cărți.",
            "books.reset": "Resetează",
            "books.empty": "Nu au fost găsite cărți pentru criteriile introduse.",
            "books.allCategories": "Toate categoriile",
            "books.found": "Cărți găsite",
            "book.details": "Detalii",

            "login.title": "Autentificare",
            "login.text": "Intră în contul tău pentru a rezerva cărți, a salva volume favorite și a accesa recomandările personalizate.",
            "login.formTitle": "Intră în cont",
            "login.button": "Autentifică-te",
            "login.noAccount": "Nu ai cont?",
            "login.createAccount": "Creează unul aici",
            "login.error.empty": "Completează emailul și parola!",
            "login.error.invalid": "Email sau parolă incorectă!",

            "footer.text": "Locul unde fiecare carte deschide o nouă lume. Platforma oferă acces rapid la lectură, recomandări și informații despre cărți.",
            "footer.contact": "Contact",
            "footer.quick": "Linkuri rapide",
            "footer.info": "Informații utile",
            "footer.news": "Abonează-te la noutăți",
            "footer.newsText": "Primește recomandări și informații despre cărțile noi adăugate în bibliotecă.",
            "footer.subscribe": "Abonează-mă",
            "footer.rights": "© 2026 Biblioteca Online. Toate drepturile rezervate.",
            "footer.terms": "Termeni și condiții",
            "footer.privacy": "Politică de confidențialitate",
            "footer.rules": "Regulament utilizare",
            "footer.help": "Ajutor utilizator",

            "register.infoTitle": "Biblioteca Online",
            "register.infoText1": "Creează un cont pentru a avea acces la rezervări, cărți favorite și recomandări personalizate.",
            "register.infoText2": "Platforma îți permite să găsești mai ușor cărțile dorite și să le salvezi pentru lectură.",
            "register.title": "Înregistrare",
            "register.button": "Creează contul",
            "register.hasAccount": "Ai deja cont?",
            "register.loginHere": "Autentifică-te aici",
            "register.error.empty": "Completați toate câmpurile!",
            "register.error.email": "Introduceți o adresă de email validă!",
            "register.error.passwordLength": "Parola trebuie să conțină minimum 6 caractere!",
            "register.error.passwordMatch": "Parolele nu coincid!",
            "register.error.exists": "Acest email este deja înregistrat!",
            "register.success.created": "Contul a fost creat cu succes! Te poți autentifica.",

            "details.category": "Categorie",
            "details.section": "Secțiune",
            "details.rating": "Notă",
            "details.description": "Descriere",
            "details.reserve": "Rezervă cartea",
            "details.loginToReserve": "Autentifică-te pentru rezervare",
            "details.backToBooks": "Înapoi la cărți",
            "details.error.alreadyReserved": "Ai deja o rezervare activă pentru această carte.",
            "details.success.reserved": "Cartea a fost rezervată cu succes!",

            "user.title": "Pagina utilizatorului",
            "user.welcome": "Bine ai venit în pagina ta personală din aplicația Biblioteca Online.",
            "user.id": "ID utilizator",
            "user.name": "Nume",
            "user.email": "Email",
            "user.role": "Rol",
            "user.viewBooks": "Vezi toate cărțile",
            "user.backHome": "Înapoi la pagina principală",
            "user.reservations": "Rezervările mele",
            "user.noReservations": "Nu ai încă nicio rezervare. Intră în catalog și rezervă prima ta carte.",
            "user.reservationDate": "Data rezervării",
            "user.cancel": "Anulează",
            "user.cancelSuccess": "Rezervarea a fost anulată cu succes.",
            "user.jsonData": "Cărți disponibile în bibliotecă",
            "user.noBooks": "Nu există cărți salvate în fișierul data/items.json.",
        },

        en: {
            "nav.home": "Home",
            "nav.books": "Books",
            "nav.categories": "Categories",
            "nav.about": "About",
            "nav.contact": "Contact",

            "auth.login": "Login",
            "auth.member": "Become a member",
            "auth.hello": "Hello",
            "auth.logout": "Logout",
            "auth.dashboard": "Dashboard",

            "hero.title": "Welcome to the Online Library",
            "hero.text": "Discover extraordinary books, valuable knowledge and the joy of reading every day.",
            "hero.explore": "Explore books",
            "hero.member": "Become a member",
            "hero.myPage": "My page",

            "search.label": "Find the right book for you",
            "search.button": "Search",

            "section.recommended": "Recommended books",
            "section.forYou": "Our recommendations for you",
            "section.categories": "Popular categories",
            "view.all": "View all →",

            "member.title": "Become a member",
            "member.text": "Join our reading community and enjoy exclusive benefits:",
            "member.li1": "✓ Access to thousands of online books",
            "member.li2": "✓ Discounts and special offers",
            "member.li3": "✓ Personalized recommendations",
            "member.join": "Join now",

            "books.title": "Book catalog",
            "books.text": "Here you can see all books available in the Online Library. Search by title, author or category and open the details of each book.",
            "books.reset": "Reset",
            "books.empty": "No books were found for the selected criteria.",
            "books.allCategories": "All categories",
            "books.found": "Books found",
            "book.details": "Details",

            "login.title": "Login",
            "login.text": "Log in to your account to reserve books, save favorite titles and access personalized recommendations.",
            "login.formTitle": "Sign in to your account",
            "login.button": "Sign in",
            "login.noAccount": "Don’t have an account?",
            "login.createAccount": "Create one here",
            "login.error.empty": "Fill in your email and password!",
            "login.error.invalid": "Incorrect email or password!",

            "footer.text": "The place where every book opens a new world. The platform offers quick access to reading, recommendations and book information.",
            "footer.contact": "Contact",
            "footer.quick": "Quick links",
            "footer.info": "Useful information",
            "footer.news": "Subscribe to news",
            "footer.newsText": "Receive recommendations and information about newly added books.",
            "footer.subscribe": "Subscribe",
            "footer.rights": "© 2026 Online Library. All rights reserved.",
            "footer.terms": "Terms and conditions",
            "footer.privacy": "Privacy policy",
            "footer.rules": "Usage rules",
            "footer.help": "User help",

            "register.infoTitle": "Online Library",
            "register.infoText1": "Create an account to access reservations, favorite books and personalized recommendations.",
            "register.infoText2": "The platform helps you find the books you want more easily and save them for reading.",
            "register.title": "Registration",
            "register.button": "Create account",
            "register.hasAccount": "Already have an account?",
            "register.loginHere": "Log in here",
            "register.error.empty": "Please fill in all fields!",
            "register.error.email": "Enter a valid email address!",
            "register.error.passwordLength": "The password must contain at least 6 characters!",
            "register.error.passwordMatch": "Passwords do not match!",
            "register.error.exists": "This email is already registered!",
            "register.success.created": "The account was created successfully! You can log in.",

            "details.category": "Category",
            "details.section": "Section",
            "details.rating": "Rating",
            "details.description": "Description",
            "details.reserve": "Reserve book",
            "details.loginToReserve": "Log in to reserve",
            "details.backToBooks": "Back to books",
            "details.error.alreadyReserved": "You already have an active reservation for this book.",
            "details.success.reserved": "The book was reserved successfully!",

            "user.title": "User page",
            "user.welcome": "Welcome to your personal page in the Online Library application.",
            "user.id": "User ID",
            "user.name": "Name",
            "user.email": "Email",
            "user.role": "Role",
            "user.viewBooks": "View all books",
            "user.backHome": "Back to home page",
            "user.reservations": "My reservations",
            "user.noReservations": "You do not have any reservations yet. Open the catalog and reserve your first book.",
            "user.reservationDate": "Reservation date",
            "user.cancel": "Cancel",
            "user.cancelSuccess": "The reservation was cancelled successfully.",
            "user.jsonData": "Books available in the library",
            "user.noBooks": "There are no books saved in the data/items.json file.",
        },

        ru: {
            "nav.home": "Главная",
            "nav.books": "Книги",
            "nav.categories": "Категории",
            "nav.about": "О нас",
            "nav.contact": "Контакты",

            "auth.login": "Войти",
            "auth.member": "Стать участником",
            "auth.hello": "Привет",
            "auth.logout": "Выйти",
            "auth.dashboard": "Панель",

            "hero.title": "Добро пожаловать в Онлайн-библиотеку",
            "hero.text": "Откройте для себя интересные книги, полезные знания и радость чтения каждый день.",
            "hero.explore": "Смотреть книги",
            "hero.member": "Стать участником",
            "hero.myPage": "Моя страница",

            "search.label": "Найдите подходящую книгу",
            "search.button": "Поиск",

            "section.recommended": "Рекомендуемые книги",
            "section.forYou": "Наши рекомендации для вас",
            "section.categories": "Популярные категории",
            "view.all": "Смотреть все →",

            "member.title": "Стать участником",
            "member.text": "Присоединяйтесь к нашему сообществу читателей и получите преимущества:",
            "member.li1": "✓ Доступ к тысячам онлайн-книг",
            "member.li2": "✓ Скидки и специальные предложения",
            "member.li3": "✓ Персональные рекомендации",
            "member.join": "Зарегистрироваться",

            "books.title": "Каталог книг",
            "books.text": "Здесь вы можете увидеть все книги, доступные в Онлайн-библиотеке. Ищите по названию, автору или категории и открывайте подробную информацию о каждой книге.",
            "books.reset": "Сбросить",
            "books.empty": "Книги по выбранным критериям не найдены.",
            "books.allCategories": "Все категории",
            "books.found": "Найдено книг",
            "book.details": "Подробнее",

            "login.title": "Вход",
            "login.text": "Войдите в свой аккаунт, чтобы бронировать книги, сохранять избранные издания и получать персональные рекомендации.",
            "login.formTitle": "Войти в аккаунт",
            "login.button": "Войти",
            "login.noAccount": "Нет аккаунта?",
            "login.createAccount": "Создать аккаунт",
            "login.error.empty": "Заполните email и пароль!",
            "login.error.invalid": "Неверный email или пароль!",

            "footer.text": "Место, где каждая книга открывает новый мир. Платформа предлагает быстрый доступ к чтению, рекомендациям и информации о книгах.",
            "footer.contact": "Контакты",
            "footer.quick": "Быстрые ссылки",
            "footer.info": "Полезная информация",
            "footer.news": "Подписаться на новости",
            "footer.newsText": "Получайте рекомендации и информацию о новых книгах.",
            "footer.subscribe": "Подписаться",
            "footer.rights": "© 2026 Онлайн-библиотека. Все права защищены.",
            "footer.terms": "Условия использования",
            "footer.privacy": "Политика конфиденциальности",
            "footer.rules": "Правила использования",
            "footer.help": "Помощь пользователю",

            "register.infoTitle": "Онлайн-библиотека",
            "register.infoText1": "Создайте аккаунт, чтобы получить доступ к бронированию, избранным книгам и персональным рекомендациям.",
            "register.infoText2": "Платформа помогает быстрее находить нужные книги и сохранять их для чтения.",
            "register.title": "Регистрация",
            "register.button": "Создать аккаунт",
            "register.hasAccount": "Уже есть аккаунт?",
            "register.loginHere": "Войти здесь",
            "register.error.empty": "Заполните все поля!",
            "register.error.email": "Введите корректный email!",
            "register.error.passwordLength": "Пароль должен содержать минимум 6 символов!",
            "register.error.passwordMatch": "Пароли не совпадают!",
            "register.error.exists": "Этот email уже зарегистрирован!",
            "register.success.created": "Аккаунт успешно создан! Теперь вы можете войти.",

            "details.category": "Категория",
            "details.section": "Раздел",
            "details.rating": "Оценка",
            "details.description": "Описание",
            "details.reserve": "Забронировать книгу",
            "details.loginToReserve": "Войдите, чтобы забронировать",
            "details.backToBooks": "Назад к книгам",
            "details.error.alreadyReserved": "У вас уже есть активное бронирование этой книги.",
            "details.success.reserved": "Книга успешно забронирована!",

            "user.title": "Страница пользователя",
            "user.welcome": "Добро пожаловать на вашу личную страницу в приложении Онлайн-библиотека.",
            "user.id": "ID пользователя",
            "user.name": "Имя",
            "user.email": "Email",
            "user.role": "Роль",
            "user.viewBooks": "Смотреть все книги",
            "user.backHome": "Назад на главную страницу",
            "user.reservations": "Мои бронирования",
            "user.noReservations": "У вас пока нет бронирований. Откройте каталог и забронируйте первую книгу.",
            "user.reservationDate": "Дата бронирования",
            "user.cancel": "Отменить",
            "user.cancelSuccess": "Бронирование успешно отменено.",
            "user.jsonData": "Книги, доступные в библиотеке",
            "user.noBooks": "В файле data/items.json нет сохранённых книг.",
        }
    };

    const placeholders = {
        ro: {
            "search.placeholder": "Caută cărți, autori, categorii...",
            "search.heroPlaceholder": "Caută după titlu, autor sau cuvânt cheie...",
            "books.searchPlaceholder": "Caută după titlu, autor sau categorie...",
            "login.emailPlaceholder": "Emailul tău",
            "login.passwordPlaceholder": "Parola ta",
            "footer.email": "Adresa ta de email...",
            "register.namePlaceholder": "Numele tău",
            "register.emailPlaceholder": "Emailul tău",
            "register.passwordPlaceholder": "Parolă",
            "register.confirmPasswordPlaceholder": "Confirmă parola",
        },

        en: {
            "search.placeholder": "Search books, authors, categories...",
            "search.heroPlaceholder": "Search by title, author or keyword...",
            "books.searchPlaceholder": "Search by title, author or category...",
            "login.emailPlaceholder": "Your email",
            "login.passwordPlaceholder": "Your password",
            "footer.email": "Your email address...",
            "register.namePlaceholder": "Your name",
            "register.emailPlaceholder": "Your email",
            "register.passwordPlaceholder": "Password",
            "register.confirmPasswordPlaceholder": "Confirm password",
        },

        ru: {
            "search.placeholder": "Искать книги, авторов, категории...",
            "search.heroPlaceholder": "Поиск по названию, автору или ключевому слову...",
            "books.searchPlaceholder": "Поиск по названию, автору или категории...",
            "login.emailPlaceholder": "Ваш email",
            "login.passwordPlaceholder": "Ваш пароль",
            "footer.email": "Ваш email...",
            "register.namePlaceholder": "Ваше имя",
            "register.emailPlaceholder": "Ваш email",
            "register.passwordPlaceholder": "Пароль",
            "register.confirmPasswordPlaceholder": "Подтвердите пароль",
        }
    };

    const langButtons = document.querySelectorAll("[data-lang]");
    const savedLang = localStorage.getItem("lang") || "ro";

    function setLanguage(lang) {
        if (!translations[lang]) {
            lang = "ro";
        }

        document.documentElement.lang = lang;
        localStorage.setItem("lang", lang);

        document.querySelectorAll("[data-i18n]").forEach(function (el) {
            const key = el.getAttribute("data-i18n");

            if (translations[lang][key]) {
                el.textContent = translations[lang][key];
            }
        });

        document.querySelectorAll("[data-i18n-placeholder]").forEach(function (el) {
            const key = el.getAttribute("data-i18n-placeholder");

            if (placeholders[lang][key]) {
                el.setAttribute("placeholder", placeholders[lang][key]);
            }
        });

        langButtons.forEach(function (btn) {
            const btnLang = btn.getAttribute("data-lang");
            btn.classList.toggle("active", btnLang === lang);
        });
    }

    langButtons.forEach(function (btn) {
        btn.addEventListener("click", function () {
            const selectedLang = btn.getAttribute("data-lang");
            setLanguage(selectedLang);
        });
    });

    setLanguage(savedLang);
});