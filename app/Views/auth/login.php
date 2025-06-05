<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Iniciar Sesión</h2>
            
            <?php if (session()->has('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= session('error') ?>
                </div>
            <?php endif; ?>
            
            <?php if (session()->has('message')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= session('message') ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo base_url('entrar'); ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" value="<?= old('email') ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <?php if (session()->has('errors.email')): ?>
                        <p class="text-red-500 text-xs mt-1"><?= session('errors.email') ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
                    <input type="password" id="password" name="password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <?php if (session()->has('errors.password')): ?>
                        <p class="text-red-500 text-xs mt-1"><?= session('errors.password') ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4 flex items-center">
                    <input type="checkbox" id="remember" name="remember" value="1" 
                           class="mr-2">
                    <label for="remember" class="text-gray-700 text-sm">Recordarme</label>
                </div>
                
                <div class="mb-6">
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Iniciar Sesión
                    </button>
                </div>
                
                <div class="text-center">
                    <a href="<?= site_url('forgot-password') ?>" class="text-blue-500 text-sm hover:underline">¿Olvidaste tu contraseña?</a>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">¿No tienes una cuenta? 
                    <a href="<?= site_url('register') ?>" class="text-blue-500 hover:underline">Regístrate</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>