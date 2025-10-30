<script setup lang="ts">
//import { StackedLayout } from '@bitrix24/b24jssdk';
import theme from '#build/b24ui/stacked-layout'
</script>
<template>
  <StackedLayout>
    <!-- Хедер -->
    <StackedLayout.Header>
      <div class="header-content">
        <h1>Мое приложение</h1>
      </div>
    </StackedLayout.Header>

    <!-- Меню -->
    <StackedLayout.Menu>
      <div class="menu-content">
        <ul>
          <li><a href="#">Главная</a></li>
          <li><a href="#">Настройки</a></li>
          <li><a href="#">Помощь</a></li>
        </ul>
      </div>
    </StackedLayout.Menu>

    <!-- Контент -->
    <StackedLayout.Content>
      <div class="content-wrapper">
        <h2>Добро пожаловать!</h2>
        <p>Это основная область контента.</p>
      </div>
    </StackedLayout.Content>

    <!-- Футер -->
    <StackedLayout.Footer>
      <div class="footer-content">
        <p>© 2025 Мое приложение</p>
      </div>
    </StackedLayout.Footer>
  </StackedLayout>
</template>
<style scoped>
.header-content, .menu-content, .content-wrapper, .footer-content {
padding: 20px;
}

.menu-content ul {
list-style: none;
padding: 0;
}

.menu-content li {
margin-bottom: 10px;
}

.menu-content a {
text-decoration: none;
color: #007bff;
}

.menu-content a:hover {
text-decoration: underline;
}
</style>