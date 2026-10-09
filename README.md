# WishBox RadicalMart RetailCRM

Joomla-пакет переносит транспортный API-контракт легаси-интеграции JShopping на отдельный компонент RadicalMart.

Для работы компонента должны быть установлены общие пакеты `pkg_wishboxretailcrm` и `pkg_wishboxradicalmart`.

В пакет входят:

- `lib_wishboxradicalmartretailcrm` — сервисы интеграции RadicalMart с RetailCRM;
- `com_wishboxradicalmartretailcrm` — компонент с API-контроллером;
- `plg_webservices_wishboxrmretailcrm` — регистрация API-маршрутов Joomla.

Маршруты публикуют события Joomla с нормализованными данными запроса. Обработчик бизнес-логики должен выполнить операцию и установить аргумент события `handled` в `true`. Если обработчика нет, API возвращает HTTP 501 вместо ложного успешного ответа.

Поддерживаемые маршруты:

- `PATCH /api/index.php/v1/wishboxrmretailcrm/updateshippingnumber`;
- `PATCH /api/index.php/v1/wishboxrmretailcrm/updatetracknumber`;
- `PATCH /api/index.php/v1/wishboxrmretailcrm/order/{radicalmart_order_id}/updatestatus`;
- `POST /api/index.php/v1/wishboxrmretailcrm/products/updatequantityfromorder/{retailcrm_order_id}`;
- `POST /api/index.php/v1/wishboxrmretailcrm/products/updatequantity`;
- `PATCH /api/index.php/v1/wishboxrmretailcrm/orders/{retailcrm_order_id}/updatesumtowords`;
- `PATCH /api/index.php/v1/wishboxrmretailcrm/orders/{retailcrm_order_id}/createpaymentlink`;
- `PATCH /api/index.php/v1/wishboxrmretailcrm/orders/{retailcrm_order_id}/createrussianpostshippingorder`.

Компонент публикует одноимённые события `onWishboxRadicalMartRetailCrm*`. В аргументах события доступны `subject`, `payload` и `handled`.

Маршруты Ozon, Wildberries и VK из легаси-плагина намеренно не перенесены: они не относятся к интеграции RadicalMart с RetailCRM.
