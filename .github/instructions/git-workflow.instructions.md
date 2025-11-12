---
description: "Git workflow и стандарты для app.cassoft.ru"
---

# Git Workflow для app.cassoft.ru

## Сервер и пути
- **Сервер:** `bitirx-brokci@83.220.174.34`
- **Путь на сервере:** `/var/www/bitirx-brokci/data/www/app.cassoft.ru`
- **SSH ключ:** `C:\Users\Asus\.ssh\id_ed25519_cassoft`
- **GitHub:** будет создан (например: `cassoft-support/appPro`)

## Основная ветка
- Используйте `main` (не `master`)
- Все разработки в отдельных ветках
- Планируется ветка `dev` для разработки

## Подключение к серверу
```bash
# Windows PowerShell
ssh -i "C:\Users\Asus\.ssh\id_ed25519_cassoft" bitirx-brokci@83.220.174.34

# Переход в проект
cd /var/www/bitirx-brokci/data/www/app.cassoft.ru
```

## Работа с Git на сервере
```bash
# Проверка статуса
git status

# Получение изменений с GitHub
git pull origin main

# Просмотр истории
git log --oneline -10
```

## Работа локально
```bash
# Клонирование (после создания GitHub репозитория)
cd c:\projects\appsOld
git clone [URL] app_new

# Обычный workflow
cd c:\projects\appsOld\app_new
git pull origin main
git add .
git commit -m "Описание изменений на русском"
git push origin main
```

## .gitignore правила
```gitignore
# Ядро Битрикс24
/bitrix/

# Загруженные файлы
/upload/

# Конфигурация БД
.settings.php
.settings_extra.php

# Логи
*.log
log*.txt

# Временные файлы
/pub/
/tmp/

# IDE
.idea/
.vscode/*
!.vscode/settings.json
!.vscode/extensions.json

# OS
.DS_Store
Thumbs.db
```

## Формат коммитов
**На русском языке!**

```bash
# Хорошие примеры:
git commit -m "Добавлен компонент загрузки фотографий"
git commit -m "Исправлена ошибка 414 URI Too Large"
git commit -m "Обновлена документация REST API"
git commit -m "Увеличены PHP лимиты до 50MB"

# Плохие примеры (не делать так):
git commit -m "fix"
git commit -m "update"
git commit -m "changes"
```

## Что НЕ коммитить
- ❌ `/bitrix/` - ядро Битрикс24
- ❌ `/upload/` - загруженные файлы пользователей
- ❌ `.settings.php` - настройки БД (пароли!)
- ❌ `*.log` - лог-файлы
- ❌ `/pub/` - публичные временные файлы
- ❌ Большие файлы (>50MB) - проверяйте перед коммитом

## Проверка перед коммитом
```bash
# Проверить размер файлов
git ls-files --cached | xargs du -h | sort -rh | head -20

# Проверить, что не добавлены лишние файлы
git status

# Проверить diff
git diff --cached
```

## Автодеплой (планируется)
После настройки GitHub Actions:
- Push в `main` → автодеплой на production
- Push в `dev` → автодеплой на dev сервер (если будет)
- Кеш Битрикс24 очищается автоматически

## Работа с большими файлами
Если файл >100MB (лимит GitHub):
```bash
# Удалить из истории (если уже закоммитили)
git filter-branch --force --index-filter \
  'git rm --cached --ignore-unmatch path/to/large/file.txt' \
  --prune-empty --tag-name-filter cat -- --all

# Force push (ОСТОРОЖНО!)
git push origin main --force
```

## SSH config для GitHub (будет настроено)
```bash
# На сервере создать ~/.ssh/config
Host github.com-app
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_ed25519_app
```

## Восстановление после ошибок
```bash
# Отменить последний коммит (не отправленный на GitHub)
git reset --soft HEAD~1

# Отменить изменения в файле
git checkout -- filename.php

# Вернуться к последнему коммиту
git reset --hard HEAD
```

## Порядок первой настройки
1. ✅ Создать `.gitignore` на сервере
2. ✅ Создать `.github/` структуру
3. ✅ `git init`
4. ✅ `git branch -M main`
5. ✅ `git add .`
6. ✅ `git commit -m "Initial commit: app.cassoft.ru project"`
7. ✅ Создать SSH ключ для GitHub
8. ✅ Создать GitHub репозиторий
9. ✅ Добавить remote: `git remote add origin [URL]`
10. ✅ `git push -u origin main`
