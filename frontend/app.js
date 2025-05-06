document.addEventListener('DOMContentLoaded', function() {
    // Cek apakah user sudah login
    const token = localStorage.getItem(TOKEN_KEY);
    if (token) {
        document.getElementById('authSection').style.display = 'block';
        document.getElementById('loginSection').style.display = 'none';
        loadFilms();
    } else {
        document.getElementById('authSection').style.display = 'none';
        document.getElementById('loginSection').style.display = 'block';
    }

    // Form login
    document.getElementById('loginForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        fetch(`${API_URL}/auth/generate_token.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.jwt) {
                localStorage.setItem(TOKEN_KEY, data.jwt);
                window.location.href = 'index.php';
            } else {
                alert('Login failed');
            }
        });
    });

    // Form register
    document.getElementById('registerForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        fetch(`${API_URL}/auth/register.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'User created successfully') {
                window.location.href = 'login.php';
            } else {
                alert('Register failed');
            }
        });
    });

    // Logout
    document.getElementById('logoutButton')?.addEventListener('click', function() {
        localStorage.removeItem(TOKEN_KEY);
        window.location.href = 'login.php';
    });

    // Tambah film
    document.getElementById('addFilmForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const judul_film = document.getElementById('judul_film').value;
        const year_film = document.getElementById('year_film').value;
        const genre_film = document.getElementById('genre_film').value;

        fetch(`${API_URL}/create.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem(TOKEN_KEY)}`
            },
            body: JSON.stringify({ judul_film, year_film, genre_film })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            loadFilms();
        });
    });

    // Load films
    function loadFilms() {
        fetch(`${API_URL}/read.php`, {
            method: 'GET',
            headers: { 'Authorization': `Bearer ${localStorage.getItem(TOKEN_KEY)}` }
        })
        .then(response => response.json())
        .then(data => {
            const filmTableBody = document.querySelector('#filmTable tbody');
            filmTableBody.innerHTML = '';

            data.body.forEach(film => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${film.judul_film}</td>
                    <td>${film.year_film}</td>
                    <td>${film.genre_film}</td>
                    <td>
                        <button onclick="deleteFilm(${film.id_film})">Delete</button>
                    </td>
                `;
                filmTableBody.appendChild(row);
            });
        });
    }

    // Delete film
    window.deleteFilm = function(id) {
        fetch(`${API_URL}/delete.php`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem(TOKEN_KEY)}`
            },
            body: JSON.stringify({ id_film: id })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            loadFilms();
        });
    };
});
