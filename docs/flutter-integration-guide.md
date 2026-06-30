# Flutter Integration Guide — JPP Makmal User API

This guide shows how to connect a Flutter app to the user API: log in,
store the token, attach it to every request, browse items, and submit a
loan application (permohonan), then track its status.

All endpoints are under `/api/v1` and (except `login`) require a Sanctum
bearer token.

## 1. Packages

```yaml
# pubspec.yaml
dependencies:
  dio: ^5.4.0
  flutter_secure_storage: ^9.0.0
```

## 2. Base URL

All endpoints are prefixed with `/api/v1`. Example base URL during local
development with Laragon: `http://jpp-makmal.test/api/v1`.

## 3. API client with token interceptor

```dart
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiClient {
  ApiClient() {
    dio = Dio(BaseOptions(
      baseUrl: 'http://jpp-makmal.test/api/v1',
      headers: {'Accept': 'application/json'},
    ));

    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _storage.read(key: 'token');
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
    ));
  }

  late final Dio dio;
  final _storage = const FlutterSecureStorage();
}
```

## 4. Login → store the token

The user only types email + password. The app stores the returned token
and never shows it.

```dart
class AuthService {
  AuthService(this._client);
  final ApiClient _client;
  final _storage = const FlutterSecureStorage();

  Future<void> login(String email, String password) async {
    final res = await _client.dio.post('/login', data: {
      'email': email,
      'password': password,
      'device_name': 'flutter-app',
    });
    await _storage.write(key: 'token', value: res.data['token']);
  }

  Future<void> logout() async {
    await _client.dio.post('/logout');
    await _storage.delete(key: 'token');
  }
}
```

Response shape from `POST /login`:

```json
{
  "token": "12|aBcD...",
  "user": { "id": 5, "name": "Ali", "email": "ali@jpp.gov.my",
            "district": { "id": 3, "name": "Hulu Langat" } }
}
```

On `422` the credentials are wrong (`errors.email`); on `429` the user
hit the login throttle (5 attempts / 15 minutes).

## 5. Browse available items

```dart
Future<List<dynamic>> fetchItems({String? search}) async {
  final res = await _client.dio.get('/items', queryParameters: {
    if (search != null && search.isNotEmpty) 'search': search,
  });
  return res.data['data']; // paginated; meta in res.data['meta']
}
```

Each item: `{ id, name, description, category, available_quantity, condition, image_url }`.

> The list and the detail endpoint only return items that are in the
> catalogue (active, available, in stock). `GET /items/{id}` returns
> `404` for an item that is inactive, borrowed, or out of stock.

## 6. Submit a loan application (permohonan)

```dart
Future<Map<String, dynamic>> submitApplication({
  required List<Map<String, int>> items, // [{ 'item_id': 12, 'quantity': 2 }]
  required String startDate,             // 'YYYY-MM-DD'
  required String endDate,
  required String purpose,               // min 10 characters
}) async {
  final res = await _client.dio.post('/loan-applications', data: {
    'items': items,
    'start_date': startDate,
    'end_date': endDate,
    'purpose': purpose,
  });
  return res.data['data']; // 201 Created → the new application
}
```

Every `422` response uses the same envelope, so the client can handle
errors uniformly:
```json
{ "message": "...", "errors": { "<field>": ["..."] } }
```
- Field validation: e.g. `errors.purpose`, `errors.end_date`.
- Insufficient stock: `errors.items` (message also in `message`, e.g.
  `"Stok Mikroskop tidak mencukupi. Tersedia: 1"`).
- Account has no district: `errors.district`.

## 7. View own applications & status

```dart
Future<List<dynamic>> fetchMyApplications() async {
  final res = await _client.dio.get('/loan-applications');
  return res.data['data'];
}

Future<Map<String, dynamic>> fetchApplication(int id) async {
  final res = await _client.dio.get('/loan-applications/$id');
  return res.data['data'];
}
```

`status` values: `menunggu`, `diluluskan`, `ditolak`, `dibatalkan`,
`dipinjam`, `dikembalikan`. Requesting an application that belongs to
another user returns `403`.

## 8. End-to-end flow

1. `AuthService.login(email, password)` → token stored.
2. `fetchItems()` → user picks items + quantities.
3. `submitApplication(...)` → `201`, status `menunggu`.
4. `fetchMyApplications()` / `fetchApplication(id)` → track status.
5. `AuthService.logout()` → token revoked + cleared.

## 9. Endpoint reference

| Method | Path | Auth | Purpose |
|--------|------|:----:|---------|
| POST | `/api/v1/login` | — | email+password → token + user |
| POST | `/api/v1/logout` | ✅ | revoke current token |
| GET | `/api/v1/user` | ✅ | current user |
| GET | `/api/v1/items` | ✅ | available items (`search`, `category_id`, `page`) |
| GET | `/api/v1/items/{id}` | ✅ | available item detail (404 if not in catalogue) |
| GET | `/api/v1/loan-applications` | ✅ | own applications |
| POST | `/api/v1/loan-applications` | ✅ | submit application |
| GET | `/api/v1/loan-applications/{id}` | ✅ | own application detail (403 if not owner) |
