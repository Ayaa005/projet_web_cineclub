<?php
require 'config/db.php';
session_start();
if (isset($_SESSION['user_id'])) { header("Location: /cineclub/index.php"); exit; }

$error = $_GET['error'] ?? '';
$tab   = $_GET['tab']   ?? 'login';
$role  = $_GET['role']  ?? 'organizer';

$msgs = [
    'wrong'         => 'Email ou mot de passe incorrect.',
    'empty'         => 'Veuillez remplir tous les champs.',
    'short_pass'    => 'Mot de passe trop court (min 6 caractères).',
    'email_taken'   => 'Cet email est déjà utilisé.',
    'invalid_code'  => 'Code d\'invitation invalide.',
    'wrong_role'    => 'Ce compte n\'existe pas avec ce rôle. Vérifiez l\'onglet sélectionné.',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>CineClub — Organisez vos soirées cinéma</title>
    <link rel="stylesheet" href="/cineclub/css/style.css">
</head>
<body class="welcome-page">

<!-- ═══════ HERO ═══════ -->
<section class="welcome-hero">
    <div class="welcome-hero-bg"></div>

    <div class="welcome-hero-nav">
        <div class="welcome-hero-nav-logo">CINECLUB</div>
    </div>

    <div class="welcome-hero-content">
        <h1>Vos soirées cinéma,<br>organisées ensemble</h1>
        <h2>Votez · Planifiez · Snackez</h2>
        <p>Invitez vos amis, choisissez le film, gérez les snacks.<br>Tout en un seul endroit. Gratuit.</p>

        <!-- ═══ BOÎTE LOGIN ═══ -->
        <div class="welcome-login-box">

            <?php if ($error): ?>
            <div class="wl-error"><?= htmlspecialchars($msgs[$error] ?? 'Erreur.') ?></div>
            <?php endif; ?>

            <!-- Tabs principaux : Se connecter / Créer un compte -->
            <div class="wl-tabs">
                <button class="wl-tab <?= $tab==='login'?'active':'' ?>"
                        onclick="setTab('login')">Se connecter</button>
                <button class="wl-tab <?= $tab==='register'?'active':'' ?>"
                        onclick="setTab('register')">Créer un compte</button>
            </div>

            <!-- ════ CRÉER UN COMPTE (simple, sans rôle) ════ -->
            <div id="form-register" <?= $tab!=='register'?'style="display:none"':'' ?>>
                <p style="font-size:12px;color:rgba(255,255,255,.5);margin-bottom:14px;line-height:1.5">
                    Crée ton compte. Ensuite tu pourras te connecter comme
                    <strong>organisateur</strong> (ta propre interface) ou comme
                    <strong>membre</strong> (rejoindre l'interface d'un ami).
                </p>
                <form method="POST" action="/cineclub/actions/register_action.php">
                    <input class="wl-input" type="text"  name="username"
                           placeholder="Ton prénom" required>
                    <input class="wl-input" type="email" name="email"
                           placeholder="Email" required>
                    <div class="wl-pwd">
                        <input class="wl-input" type="password" name="password"
                               id="rp" placeholder="Mot de passe (min 6 caractères)"
                               required style="margin-bottom:0">
                        <button type="button" class="wl-pwd-toggle" onclick="tog('rp')">👁</button>
                    </div>
                    <button type="submit" class="wl-submit" style="margin-top:14px">
                        Créer mon compte →
                    </button>
                </form>
                <p class="wl-footer">
                    Déjà un compte ?
                    <a href="#" onclick="setTab('login');return false">Se connecter</a>
                </p>
            </div>

            <!-- ════ SE CONNECTER ════ -->
            <div id="form-login" <?= $tab!=='login'?'style="display:none"':'' ?>>

                <!-- Sous-onglets : Organisateur / Membre -->
                <div class="wl-tabs" style="margin-bottom:16px">
                    <button class="wl-tab <?= $role==='organizer'?'active':'' ?>"
                            id="rt-org" onclick="setRole('organizer')">
                        👑 Organisateur
                    </button>
                    <button class="wl-tab <?= $role==='member'?'active':'' ?>"
                            id="rt-mem" onclick="setRole('member')">
                        👤 Membre
                    </button>
                </div>

                <!-- Description selon rôle -->
                <p id="desc-org" style="font-size:12px;color:rgba(255,255,255,.45);margin-bottom:12px;<?= $role==='member'?'display:none':'' ?>">
                    Tu accèdes à <strong>ton interface personnelle</strong>. Si c'est ta première connexion, ton espace sera vide et prêt à configurer.
                </p>
                <p id="desc-mem" style="font-size:12px;color:rgba(255,255,255,.45);margin-bottom:12px;<?= $role==='organizer'?'display:none':'' ?>">
                    Tu accèdes à <strong>l'interface d'un organisateur</strong> grâce au code qu'il t'a envoyé.
                </p>

                <form method="POST" action="/cineclub/actions/login_action.php">
                    <input type="hidden" name="login_type" id="login_type"
                           value="<?= $role ?>">

                    <input class="wl-input" type="email" name="email"
                           placeholder="Email" required>

                    <div class="wl-pwd">
                        <input class="wl-input" type="password" name="password"
                               id="lp" placeholder="Mot de passe"
                               required style="margin-bottom:0">
                        <button type="button" class="wl-pwd-toggle" onclick="tog('lp')">👁</button>
                    </div>

                    <!-- Code invitation : visible uniquement si Membre -->
                    <div id="code-field" style="<?= $role==='organizer'?'display:none':'' ?>">
                        <input class="wl-input" type="text" name="invite_code"
                               id="invite_code"
                               placeholder="Code invitation (ex : ABCD-XYZ9)"
                               style="text-transform:uppercase;letter-spacing:4px;
                                      text-align:center;font-size:16px;
                                      font-weight:700;margin-top:4px">
                    </div>

                    <button type="submit" class="wl-submit" id="submit-btn">
                        <?= $role==='organizer' ? 'Se connecter comme organisateur' : 'Rejoindre l\'interface' ?>
                    </button>
                </form>

                <p class="wl-footer">
                    Pas encore de compte ?
                    <a href="#" onclick="setTab('register');return false">Créer un compte</a>
                </p>
            </div>

        </div><!-- fin welcome-login-box -->
    </div><!-- fin welcome-hero-content -->
</section>

<!-- Divider rouge -->
<div class="welcome-divider"></div>

<!-- Features -->
<section class="welcome-features">
    <div class="wf-card">
        <h3>Votez pour le prochain film</h3>
        <p>Suggérez des films et votez avec vos amis. Le plus voté est sélectionné.</p>
        <div class="wf-icon">🗳</div>
    </div>
    <div class="wf-card">
        <h3>Organisez les snacks</h3>
        <p>Chaque membre se porte volontaire pour un snack. L'organisateur confirme.</p>
        <div class="wf-icon">🍿</div>
    </div>
    <div class="wf-card">
        <h3>Code d'invitation unique</h3>
        <p>Chaque organisateur a un code fixe et permanent. Vos amis l'utilisent pour vous rejoindre.</p>
        <div class="wf-icon">🎟</div>
    </div>
    <div class="wf-card">
        <h3>Plusieurs organisateurs</h3>
        <p>Chaque personne peut créer sa propre interface et inviter ses propres membres.</p>
        <div class="wf-icon">🎬</div>
    </div>
</section>

<div class="welcome-divider"></div>

<!-- FAQ -->
<section class="welcome-faq">
    <h2>Questions fréquentes</h2>
    <?php foreach ([
        ["Puis-je être organisateur ET membre ?",
         "Oui ! Avec le même compte, connecte-toi en tant qu'organisateur pour gérer tes soirées, ou en tant que membre pour rejoindre celles d'un ami avec son code d'invitation."],
        ["Comment inviter des amis ?",
         "Connecte-toi comme organisateur. Depuis la page Participants, tu trouveras ton code d'invitation permanent. Partage-le par WhatsApp."],
        ["Le code d'invitation change-t-il ?",
         "Non. Chaque organisateur a UN code FIXE et UNIQUE. Il ne change jamais. Vos amis peuvent l'utiliser à tout moment."],
        ["Que se passe-t-il à ma première connexion comme organisateur ?",
         "Ton interface est complètement vide. À toi de la personnaliser : ajouter des films, créer une session, inviter tes membres."],
    ] as $i => $f): ?>
    <div class="faq-item">
        <div class="faq-q" onclick="tFaq(<?=$i?>)">
            <span><?=$f[0]?></span>
            <span class="faq-icon" id="fi-<?=$i?>">+</span>
        </div>
        <div class="faq-a" id="fa-<?=$i?>"><?=$f[1]?></div>
    </div>
    <?php endforeach; ?>
    <div class="faq-end"></div>
</section>

<!-- CTA bas de page -->
<div class="welcome-cta">
    <div>
        <p>Prêt pour votre prochaine soirée cinéma ?</p>
        <span>Rejoignez CineClub gratuitement dès maintenant.</span>
    </div>
    <div style="display:flex;gap:10px">
        <a href="#" onclick="setTab('register');scrollUp();return false" class="btn-red">
            Créer un compte
        </a>
        <a href="#" onclick="setTab('login');scrollUp();return false" class="btn-dark">
            Se connecter
        </a>
    </div>
</div>

<footer style="background:#000;padding:20px 60px;border-top:1px solid #222;">
    <p style="color:#555;font-size:13px;text-align:center;">© 2026 CineClub</p>
</footer>

<script>
function setTab(t) {
    document.getElementById('form-login').style.display    = t==='login'    ? '' : 'none';
    document.getElementById('form-register').style.display = t==='register' ? '' : 'none';
    document.querySelectorAll('.wl-tab').forEach((b,i) =>
        b.classList.toggle('active',
            (t==='login'&&i===0)||(t==='register'&&i===1)));
}

function setRole(r) {
    document.getElementById('login_type').value = r;
    document.getElementById('rt-org').classList.toggle('active', r==='organizer');
    document.getElementById('rt-mem').classList.toggle('active', r==='member');
    document.getElementById('desc-org').style.display   = r==='organizer' ? '' : 'none';
    document.getElementById('desc-mem').style.display   = r==='member'    ? '' : 'none';
    document.getElementById('code-field').style.display = r==='member'    ? '' : 'none';
    const code = document.getElementById('invite_code');
    if (r==='member') code.setAttribute('required','');
    else code.removeAttribute('required');
    document.getElementById('submit-btn').textContent =
        r==='organizer' ? 'Se connecter comme organisateur' : 'Rejoindre l\'interface';
}

function tog(id) {
    const i = document.getElementById(id);
    i.type = i.type==='password' ? 'text' : 'password';
}

function tFaq(i) {
    const a=document.getElementById('fa-'+i), ic=document.getElementById('fi-'+i);
    const o=a.classList.contains('open');
    a.classList.toggle('open',!o); ic.classList.toggle('open',!o);
    ic.textContent = o ? '+' : '×';
}

function scrollUp() {
    document.querySelector('.welcome-hero-content').scrollIntoView({behavior:'smooth'});
}

// Restaurer l'état selon les paramètres GET
<?php if ($tab==='register'): ?>setTab('register');<?php endif; ?>
<?php if ($tab==='login' && $role==='member'): ?>setRole('member');<?php endif; ?>
</script>
</body>
</html>
