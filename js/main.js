/* ─── PORTFOLIO MAIN JS ─────────────────────────────────── */

/* ─── CURSOR ─────────────────────────────────────────────── */
const cursor = document.getElementById('cursor');
const follower = document.getElementById('cursorFollower');
let mx = 0, my = 0, fx = 0, fy = 0;

document.addEventListener('mousemove', e => {
  mx = e.clientX; my = e.clientY;
  cursor.style.left = mx + 'px';
  cursor.style.top  = my + 'px';
});

function animateFollower() {
  fx += (mx - fx) * 0.12;
  fy += (my - fy) * 0.12;
  follower.style.left = fx + 'px';
  follower.style.top  = fy + 'px';
  requestAnimationFrame(animateFollower);
}
animateFollower();

document.querySelectorAll('a, button, [role="button"]').forEach(el => {
  el.addEventListener('mouseenter', () => {
    cursor.style.transform = 'translate(-50%,-50%) scale(2)';
    follower.style.opacity = '0.2';
  });
  el.addEventListener('mouseleave', () => {
    cursor.style.transform = 'translate(-50%,-50%) scale(1)';
    follower.style.opacity = '0.5';
  });
});

/* ─── THEME TOGGLE ───────────────────────────────────────── */
const themeToggle = document.getElementById('themeToggle');
const body = document.body;
const THEME_KEY = 'ic_theme';

function applyTheme(dark) {
  body.classList.toggle('dark-mode', dark);
  body.classList.toggle('light-mode', !dark);
  themeToggle.querySelector('.toggle-icon').textContent = dark ? '☽' : '☀';
  document.cookie = `${THEME_KEY}=${dark ? 'dark' : 'light'};path=/;max-age=31536000`;
  localStorage.setItem(THEME_KEY, dark ? 'dark' : 'light');
}

// Read saved preference (cookie first, then localStorage)
function getSavedTheme() {
  const cookie = document.cookie.split('; ').find(r => r.startsWith(THEME_KEY + '='));
  if (cookie) return cookie.split('=')[1];
  return localStorage.getItem(THEME_KEY);
}

const savedTheme = getSavedTheme();
applyTheme(savedTheme ? savedTheme === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches);

themeToggle.addEventListener('click', () => {
  applyTheme(!body.classList.contains('dark-mode'));
});

/* ─── HAMBURGER MENU ─────────────────────────────────────── */
const hamburger = document.getElementById('hamburger');
const navLinks  = document.getElementById('navLinks');

hamburger.addEventListener('click', () => {
  navLinks.classList.toggle('open');
  const spans = hamburger.querySelectorAll('span');
  const open = navLinks.classList.contains('open');
  spans[0].style.transform = open ? 'rotate(45deg) translate(5px,5px)' : '';
  spans[1].style.opacity = open ? '0' : '1';
  spans[2].style.transform = open ? 'rotate(-45deg) translate(5px,-5px)' : '';
});

navLinks.querySelectorAll('a').forEach(a => {
  a.addEventListener('click', () => {
    navLinks.classList.remove('open');
    const spans = hamburger.querySelectorAll('span');
    spans[0].style.transform = ''; spans[1].style.opacity = '1'; spans[2].style.transform = '';
  });
});

/* ─── HEADER SCROLL ──────────────────────────────────────── */
const header = document.getElementById('header');
window.addEventListener('scroll', () => {
  header.style.boxShadow = window.scrollY > 20 ? '0 4px 30px rgba(0,0,0,0.08)' : 'none';
});

/* ─── REVEAL ON SCROLL ───────────────────────────────────── */
const revealEls = document.querySelectorAll('.section-label, .section-title, .about-text, .about-stats, .skill-item, .stat-card, .contact-info, .contact-form, .project-card');
revealEls.forEach(el => el.classList.add('reveal'));

const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach((entry, i) => {
    if (entry.isIntersecting) {
      setTimeout(() => entry.target.classList.add('visible'), i * 60);
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.1 });
revealEls.forEach(el => revealObserver.observe(el));

/* ─── SKILL BAR ANIMATION ────────────────────────────────── */
const skillObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('animated');
      skillObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.3 });
document.querySelectorAll('.skill-item').forEach(el => skillObserver.observe(el));

/* ─── COUNTER ANIMATION ──────────────────────────────────── */
function animateCounter(el) {
  const target = parseInt(el.dataset.target);
  const duration = 1500;
  const step = target / (duration / 16);
  let current = 0;
  const timer = setInterval(() => {
    current = Math.min(current + step, target);
    el.textContent = Math.floor(current) + (el.dataset.target === '100' ? '%' : '+');
    if (current >= target) clearInterval(timer);
  }, 16);
}

const counterObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      animateCounter(entry.target);
      counterObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.5 });
document.querySelectorAll('.stat-num').forEach(el => counterObserver.observe(el));

/* ─── LOAD PROJECTS VIA AJAX ─────────────────────────────── */
function loadProjects() {
  fetch('php/get_projects.php')
    .then(res => {
      if (!res.ok) throw new Error('Network error');
      return res.json();
    })
    .then(data => {
      const grid = document.getElementById('projectsGrid');
      if (!data || data.length === 0) {
        grid.innerHTML = '<p class="projects-loading">Henüz proje eklenmemiş.</p>';
        return;
      }
      grid.innerHTML = data.map((p, i) => `
        <div class="project-card reveal">
          <div class="project-num">${String(i + 1).padStart(2, '0')}</div>
          <h3 class="project-title">${escHtml(p.title)}</h3>
          <p class="project-desc">${escHtml(p.description)}</p>
          <div class="project-tags">
            ${(p.technologies || '').split(',').map(t => `<span class="project-tag">${escHtml(t.trim())}</span>`).join('')}
          </div>
          <div class="project-links">
            ${p.github_url ? `<a href="${escHtml(p.github_url)}" target="_blank" class="project-link">GitHub →</a>` : ''}
            ${p.live_url   ? `<a href="${escHtml(p.live_url)}"   target="_blank" class="project-link">Live Demo →</a>` : ''}
          </div>
        </div>
      `).join('');
      // Animate newly added cards
      grid.querySelectorAll('.project-card').forEach(el => revealObserver.observe(el));
    })
    .catch(() => {
      document.getElementById('projectsGrid').innerHTML = `
        <div class="project-card">
          <div class="project-num">01</div>
          <h3 class="project-title">Portfolio Web Sitesi</h3>
          <p class="project-desc">HTML5, CSS3, JavaScript, PHP ve MySQL kullanılarak geliştirilen tam yığın portfolyo projesi.</p>
          <div class="project-tags">
            <span class="project-tag">HTML5</span>
            <span class="project-tag">CSS3</span>
            <span class="project-tag">PHP</span>
            <span class="project-tag">MySQL</span>
          </div>
          <div class="project-links">
            <a href="https://github.com/cibrahim58" target="_blank" class="project-link">GitHub →</a>
          </div>
        </div>
        <div class="project-card">
          <div class="project-num">02</div>
          <h3 class="project-title">Görev Yönetim Uygulaması</h3>
          <p class="project-desc">JavaScript ile geliştirilmiş, localStorage destekli görev takip uygulaması.</p>
          <div class="project-tags">
            <span class="project-tag">JavaScript</span>
            <span class="project-tag">DOM</span>
            <span class="project-tag">CSS Grid</span>
          </div>
        </div>
        <div class="project-card">
          <div class="project-num">03</div>
          <h3 class="project-title">Öğrenci Veri Sistemi</h3>
          <p class="project-desc">PHP ve MySQL ile geliştirilen CRUD özellikli öğrenci kayıt yönetim sistemi.</p>
          <div class="project-tags">
            <span class="project-tag">PHP</span>
            <span class="project-tag">MySQL</span>
            <span class="project-tag">AJAX</span>
          </div>
        </div>
      `;
      grid.querySelectorAll('.project-card').forEach(el => {
        el.classList.add('reveal');
        revealObserver.observe(el);
      });
    });
}
loadProjects();

/* ─── CONTACT FORM VALIDATION ────────────────────────────── */
const form = document.getElementById('contactForm');

function showError(fieldId, errorId, message) {
  const field = document.getElementById(fieldId);
  const error = document.getElementById(errorId);
  field.classList.add('error');
  error.textContent = message;
}
function clearError(fieldId, errorId) {
  document.getElementById(fieldId).classList.remove('error');
  document.getElementById(errorId).textContent = '';
}
function validateForm() {
  let valid = true;
  const name    = document.getElementById('name').value.trim();
  const email   = document.getElementById('email').value.trim();
  const subject = document.getElementById('subject').value.trim();
  const message = document.getElementById('message').value.trim();
  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  clearError('name',    'nameError');
  clearError('email',   'emailError');
  clearError('subject', 'subjectError');
  clearError('message', 'messageError');

  if (!name || name.length < 2) {
    showError('name', 'nameError', 'Ad en az 2 karakter olmalıdır.'); valid = false;
  }
  if (!email || !emailRe.test(email)) {
    showError('email', 'emailError', 'Geçerli bir e-posta adresi girin.'); valid = false;
  }
  if (!subject || subject.length < 3) {
    showError('subject', 'subjectError', 'Konu en az 3 karakter olmalıdır.'); valid = false;
  }
  if (!message || message.length < 10) {
    showError('message', 'messageError', 'Mesaj en az 10 karakter olmalıdır.'); valid = false;
  }
  return valid;
}

// Real-time validation
['name','email','subject','message'].forEach(id => {
  document.getElementById(id).addEventListener('input', () => {
    if (document.getElementById(id).classList.contains('error')) validateForm();
  });
});

form.addEventListener('submit', e => {
  e.preventDefault();
  if (!validateForm()) return;

  const btn   = document.getElementById('submitBtn');
  const sMsg  = document.getElementById('formSuccess');
  const eMsg  = document.getElementById('formErrorMsg');
  btn.querySelector('.btn-text').style.display    = 'none';
  btn.querySelector('.btn-loading').style.display = 'inline';
  btn.disabled = true;
  sMsg.style.display = 'none';
  eMsg.style.display = 'none';

  const fd = new FormData(form);

  fetch('php/submit_contact.php', { method: 'POST', body: fd })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        sMsg.style.display = 'block';
        form.reset();
      } else {
        eMsg.textContent = data.message || 'Bir hata oluştu.';
        eMsg.style.display = 'block';
      }
    })
    .catch(() => { eMsg.style.display = 'block'; })
    .finally(() => {
      btn.querySelector('.btn-text').style.display    = 'inline';
      btn.querySelector('.btn-loading').style.display = 'none';
      btn.disabled = false;
    });
});

/* ─── UTILITY ────────────────────────────────────────────── */
function escHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
