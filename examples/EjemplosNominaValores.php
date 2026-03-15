<?php
declare(strict_types=1);

/**
 * Ejemplos de Nómina por Valores
 *
 * Este archivo contiene ejemplos de cómo crear facturas de nómina (CFDI tipo "N")
 * usando valores directos (datos completos de emisor y receptor en lugar de IDs).
 * Incluye 4 variantes: ordinaria, asimilados, bonos/fondo de ahorro y horas extra.
 */

use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use Fiscalapi\Http\FiscalApiSettings;
use Fiscalapi\Services\FiscalApiClient;

require_once __DIR__ . '/../vendor/autoload.php';


// Crea las configuraciones del cliente http fiscalapi.
$settings = new FiscalApiSettings(
    'https://test.fiscalapi.com',
    '<apiKey>',
    '<tenant>',
    false,
    false,
);

// Contraseña del CSD
$password = "12345678a";

// Sellos de KARLA FUENTE NOLASCO. Ver https://docs.fiscalapi.com/tax-files-info#codificacion-de-fiel-o-csd-en-base64
$base64Cer = "MIIFgDCCA2igAwIBAgIUMzAwMDEwMDAwMDA1MDAwMDM0NDYwDQYJKoZIhvcNAQELBQAwggErMQ8wDQYDVQQDDAZBQyBVQVQxLjAsBgNVBAoMJVNFUlZJQ0lPIERFIEFETUlOSVNUUkFDSU9OIFRSSUJVVEFSSUExGjAYBgNVBAsMEVNBVC1JRVMgQXV0aG9yaXR5MSgwJgYJKoZIhvcNAQkBFhlvc2Nhci5tYXJ0aW5lekBzYXQuZ29iLm14MR0wGwYDVQQJDBQzcmEgY2VycmFkYSBkZSBjYWxpejEOMAwGA1UEEQwFMDYzNzAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBDSVVEQUQgREUgTUVYSUNPMREwDwYDVQQHDAhDT1lPQUNBTjERMA8GA1UELRMIMi41LjQuNDUxJTAjBgkqhkiG9w0BCQITFnJlc3BvbnNhYmxlOiBBQ0RNQS1TQVQwHhcNMjMwNTE4MTQzNTM3WhcNMjcwNTE4MTQzNTM3WjCBpzEdMBsGA1UEAxMUS0FSTEEgRlVFTlRFIE5PTEFTQ08xHTAbBgNVBCkTFEtBUkxBIEZVRU5URSBOT0xBU0NPMR0wGwYDVQQKExRLQVJMQSBGVUVOVEUgTk9MQVNDTzEWMBQGA1UELRMNRlVOSzY3MTIyOFBINjEbMBkGA1UEBRMSRlVOSzY3MTIyOE1DTE5MUjA1MRMwEQYDVQQLEwpTdWN1cnNhbCAxMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhNXbTSqGX6+/3Urpemyy5vVG2IdP2v7v001+c4BoMxEDFDQ32cOFdDiRxy0Fq9aR+Ojrofq8VeftvN586iyA1A6a0QnA68i7JnQKI4uJy+u0qiixuHu6u3b3BhSpoaVHcUtqFWLLlzr0yBxfVLOqVna/1/tHbQJg9hx57mp97P0JmXO1WeIqi+Zqob/mVZh2lsPGdJ8iqgjYFaFn9QVOQ1Pq74o1PTqwfzqgJSfV0zOOlESDPWggaDAYE4VNyTBisOUjlNd0x7ppcTxSi3yenrJHqkq/pqJsRLKf6VJ/s9p6bsd2bj07hSDpjlDC2lB25eEfkEkeMkXoE7ErXQ5QCwIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQsFAAOCAgEAHwYpgbClHULXYhK4GNTgonvXh81oqfXwCSWAyDPiTYFDWVfWM9C4ApxMLyc0XvJte75Rla+bPC08oYN3OlhbbvP3twBL/w9SsfxvkbpFn2ZfGSTXZhyiq4vjmQHW1pnFvGelwgU4v3eeRE/MjoCnE7M/Q5thpuog6WGf7CbKERnWZn8QsUaJsZSEkg6Bv2jm69ye57ab5rrOUaeMlstTfdlaHAEkUgLX/NXq7RbGwv82hkHY5b2vYcXeh34tUMBL6os3OdRlooN9ZQGkVIISvxVZpSHkYC20DFNh1Bb0ovjfujlTcka81GnbUhFGZtRuoVQ1RVpMO8xtx3YKBLp4do3hPmnRCV5hCm43OIjYx9Ov2dqICV3AaNXSLV1dW39Bak/RBiIDGHzOIW2+VMPjvvypBjmPv/tmbqNHWPSAWOxTyMx6E1gFCZvi+5F+BgkdC3Lm7U0BU0NfvsXajZd8sXnIllvEMrikCLoI/yurvexNDcF1RW/FhMsoua0eerwczcNm66pGjHm05p9DR6lFeJZrtqeqZuojdxBWy4vH6ghyJaupergoX+nmdG3JYeRttCFF/ITI68TeCES5V3Y0C3psYAg1XxcGRLGd4chPo/4xwiLkijWtgt0/to5ljGBwfK7r62PHZfL1Dp+i7V3w7hmOlhbXzP+zhMZn1GCk7KY=";
$base64Key = "MIIFDjBABgkqhkiG9w0BBQ0wMzAbBgkqhkiG9w0BBQwwDgQIAgEAAoIBAQACAggAMBQGCCqGSIb3DQMHBAgwggS9AgEAMASCBMh4EHl7aNSCaMDA1VlRoXCZ5UUmqErAbucRBAKNQXH8t8gVCl/ItHMI2hMJ76QOECOqEi1Y89cDpegDvh/INXyMsXbzi87tfFzgq1O+9ID6aPWGg+bNGADXyXxDVdy7Nq/SCdoXvo66MTYwq8jyJeUHDHEGMVBcmZpD44VJCvLBxDcvByuevP4Wo2NKqJCwK+ecAdZc/8Rvd947SjbMHuS8BppfQWARVUqA5BLOkTAHNv6tEk/hncC7O2YOGSShart8fM8dokgGSyewHVFe08POuQ+WDHeVpvApH/SP29rwktSoiHRoL6dK+F2YeEB5SuFW9LQgYCutjapmUP/9TC3Byro9Li6UrvQHxNmgMFGQJSYjFdqlGjLibfuguLp7pueutbROoZaSxU8HqlfYxLkpJUxUwNI1ja/1t3wcivtWknVXBd13R06iVfU1HGe8Kb4u5il4a4yP4p7VT4RE3b1SBLJeG+BxHiE8gFaaKcX/Cl6JV14RPTvk/6VnAtEQ66qHJex21KKuiJo2JoOmDXVHmvGQlWXNjYgoPx28Xd5WsofL+n7HDR2Ku8XgwJw6IXBJGuoday9qWN9v/k7DGlNGB6Sm4gdVUmycMP6EGhB1vFTiDfOGQO42ywmcpKoMETPVQ5InYKE0xAOckgcminDgxWjtUHjBDPEKifEjYudPwKmR6Cf4ZdGvUWwY/zq9pPAC9bu423KeBCnSL8AQ4r5SVsW6XG0njamwfNjpegwh/YG7sS7sDtZ8gi7r6tZYjsOqZlCYU0j7QTBpuQn81Yof2nQRCFxhRJCeydmIA8+z0nXrcElk7NDPk4kYQS0VitJ2qeQYNENzGBglROkCl2y6GlxAG80IBtReCUp/xOSdlwDR0eim+SNkdStvmQM5IcWBuDKwGZc1A4v/UoLl7niV9fpl4X6bUX8lZzY4gidJOafoJ30VoY/lYGkrkEuz3GpbbT5v8fF3iXVRlEqhlpe8JSGu7Rd2cPcJSkQ1Cuj/QRhHPhFMF2KhTEf95c9ZBKI8H7SvBi7eLXfSW2Y0ve6vXBZKyjK9whgCU9iVOsJjqRXpAccaWOKi420CjmS0+uwj/Xr2wLZhPEjBA/G6Od30+eG9mICmbp/5wAGhK/ZxCT17ZETyFmOMo49jl9pxdKocJNuzMrLpSz7/g5Jwp8+y8Ck5YP7AX0R/dVA0t37DO7nAbQT5XVSYpMVh/yvpYJ9WR+tb8Yg1h2lERLR2fbuhQRcwmisZR2W3Sr2b7hX9MCMkMQw8y2fDJrzLrqKqkHcjvnI/TdzZW2MzeQDoBBb3fmgvjYg07l4kThS73wGX992w2Y+a1A2iirSmrYEm9dSh16JmXa8boGQAONQzQkHh7vpw0IBs9cnvqO1QLB1GtbBztUBXonA4TxMKLYZkVrrd2RhrYWMsDp7MpC4M0p/DA3E/qscYwq1OpwriewNdx6XXqMZbdUNqMP2viBY2VSGmNdHtVfbN/rnaeJetFGX7XgTVYD7wDq8TW9yseCK944jcT+y/o0YiT9j3OLQ2Ts0LDTQskpJSxRmXEQGy3NBDOYFTvRkcGJEQJItuol8NivJN1H9LoLIUAlAHBZxfHpUYx66YnP4PdTdMIWH+nxyekKPFfAT7olQ=";

// Sellos de ORGANICOS ÑAVEZ OSORIO
$organicosNavezOsorioBase64Cer = "MIIF1DCCA7ygAwIBAgIUMzAwMDEwMDAwMDA1MDAwMDM0MzkwDQYJKoZIhvcNAQELBQAwggErMQ8wDQYDVQQDDAZBQyBVQVQxLjAsBgNVBAoMJVNFUlZJQ0lPIERFIEFETUlOSVNUUkFDSU9OIFRSSUJVVEFSSUExGjAYBgNVBAsMEVNBVC1JRVMgQXV0aG9yaXR5MSgwJgYJKoZIhvcNAQkBFhlvc2Nhci5tYXJ0aW5lekBzYXQuZ29iLm14MR0wGwYDVQQJDBQzcmEgY2VycmFkYSBkZSBjYWxpejEOMAwGA1UEEQwFMDYzNzAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBDSVVEQUQgREUgTUVYSUNPMREwDwYDVQQHDAhDT1lPQUNBTjERMA8GA1UELRMIMi41LjQuNDUxJTAjBgkqhkiG9w0BCQITFnJlc3BvbnNhYmxlOiBBQ0RNQS1TQVQwHhcNMjMwNTE4MTI1NTE2WhcNMjcwNTE4MTI1NTE2WjCB+zEzMDEGA1UEAxQqT1JHQU5JQ09TINFBVkVaIE9TT1JJTyBTLkEgREUgQy5WIFNBIERFIENWMTMwMQYDVQQpFCpPUkdBTklDT1Mg0UFWRVogT1NPUklPIFMuQSBERSBDLlYgU0EgREUgQ1YxMzAxBgNVBAoUKk9SR0FOSUNPUyDRQVZFWiBPU09SSU8gUy5BIERFIEMuViBTQSBERSBDVjElMCMGA1UELRQcT9FPMTIwNzI2UlgzIC8gVkFEQTgwMDkyN0RKMzEeMBwGA1UEBRMVIC8gVkFEQTgwMDkyN0hTUlNSTDA1MRMwEQYDVQQLEwpTdWN1cnNhbCAxMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlAF4PoRqITQAEjFBzzfiT/NSN2yvb7Iv1ZMe4qD7tBxBxazRCx+GnimfpR+eaM744RlRDUj+hZfWcsOMn+q65UEIP+Xq5V1NbO1LZDse9uG1fLLSmptfKjyfvTtmBNYBjC3G6YmRv5qVw81CIS4aQOSMXKD+lrxjmRUhV9EAtXVoqGxvyDKeeX4caKuRz8mlrnR8/SMbnpobe5BNoXPrpDbEypemiJXe40pjsltY0RV3b0W0JtJQABUwZ9xn0lPYHY2q7IxYfohibv+o9ldXOXY6tivBZFfbGQSUp7CevC55+Y6uqh35Pi1o0nt/vBVgUOVPNM8d4TvGbXsE0G2J7QIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQsFAAOCAgEAFp52XykMXfFUtjQqA2zzLPrPIDSMEpkm1vWY0qfz2gC2TlVpbDCWH2vFHpP8D14OifXOmYkws2cvLyE0uBN6se4zXxVHBpTEq+93rvu/tjvMU6r7DISDwB0EX5kmKIFcOugET3/Eq1mxZ6mrI0K26RaEUz+HVyR0EQ2Ll5CLExDkPYV/am0gynhn6QPkxPNbcbm77PEIbH7zc+t7ZB5sgQ6LnubgnKNZDn8bNhkuM1jqFkh7h0owhlJrOvATgrDSLnrot8FoLFkrWQD4uA5udGRwXn5QWx0QM5ScNiSgSRilSFEyXn6rH/CJLO05Sx5OwJJTaxFbAyOXnoNdPMzbQAziaW78478nCNZVSrKWpjwWpScirtM2zcQ9fywd/a3CG66Ff29zasfhHJCp29TIjj1OURp6l1CKc16+UxjuVJ1z5Xh7v3s8S2gtmuYP1sUXPvAEYuVp9CFW87QVMtl3+nGlyJEzSAW/yaps9ua5RmyJK0Mjk1zyXjOJoIY75CIOMN8oqVAxmLJg5XftXJSekGpxybw9aq9qOJdmxVcZoAFaYg4MAdKViBoYxfWfEm4q/ihRz4asnzLp9NJWTXN1YH94rJrK7JSEq820flgr1kiL7z7n1rgWMvhJH9nHriG3yRkno/8OdLJxOSXd7MKZfZx0EWDX8toqWyE7zia8aPM=";
$organicosNavezOsorioBase64Key = "MIIFDjBABgkqhkiG9w0BBQ0wMzAbBgkqhkiG9w0BBQwwDgQIAgEAAoIBAQACAggAMBQGCCqGSIb3DQMHBAgwggS8AgEAMASCBMh4EHl7aNSCaMDA1VlRoXCZ5UUmqErAbucRFLOMmsAaFFEdAecnfgJf0IlyJpvyNOGiSwXgY6uZtS0QJmmupWTlQATxbN4xeN7csx7yCMYxMiWXLyTbjVIWzzsFVKHbsxCudz6UDqMZ3aXEEPDDbPECXJC4FxqzuUgifN4QQuIvxfPbk23m3Vtqu9lr/xMrDNqLZ4RiqY2062kgQzGzekq8CSC97qBAbb8SFMgakFjeHN0JiTGaTpYCpGbu4d+i3ZrQ0mlYkxesdvCLqlCwVM0RTMJsNQ8vpBpRDzH372iOTLCO/gXtV8pEsxpUzG9LSUBo7xSMd1/lcfdyqVgnScgUm8/+toxk6uwZkUMWWvp7tqrMYQFYdR5CjiZjgAWrNorgMmawBqkJU6KQO/CpXVn99U1fANPfQoeyQMgLt35k0JKynG8MuWsgb4EG9Z6sRmOsCQQDDMKwhBjqcbEwN2dL4f1HyN8wklFCyYy6j1NTKU2AjRMXVu4+OlAp5jpjgv08RQxEkW/tNMSSBcpvOzNr64u0M692VA2fThR3UMQ/MZ2yVM6yY3GgIu2tJmg08lhmkoLpWZIMy7bZjj/AEbi7B3wSF4vDYZJcr/Djeezm3MMSghoiOIRSqtBjwf7ZjhA2ymdCsrzy7XSMVekT0y1S+ew1WhnzUNKQSucb6V2yRwNbm0EyeEuvVyHgiGEzCrzNbNHCfoFr69YCUi8itiDfiV7/p7LJzD8J/w85nmOkI/9p+aZ2EyaOdThqBmN4CtoDi5ixz/1EElLn7KVI4d/DZsZ4ZMu76kLAy94o0m6ORSbHX5hw12+P5DgGaLu/Dxd9cctRCkvcUdagiECuKGLJpxTJvEBQoZqUB8AJFgwKcNLl3Z5KAWL5hV0t1h8i3N4HllygqpfUSQMLWCtlGwdI4XGlGI5CmnjrL2Uj8sj9C0zSNqZVnAXFMV9f2ND9W6YJqfU89BQ6Y4QQRMGjXcVF7c78bn5r6zI+Qv2QKm3YiGCfuIa64B+PB/BdithpOuBPn5X5Zxc8ju/kYjJk7sau7VtKJseGOJ1bqOq99VzaxoHjzoJgthLHtni9WtGAnnQy7GMWGW4Un2yObHCxvQxx/rIZEaQiCGfRXOcZIZuXBe5xeHJFGrekDxu3YyumEnLWvsirDF3qhpUtxqvbkTuZw2xT3vTR+oWZpSEnYTd3k/09Eb0ovOPLkbhvcvCEeoI91EJvU+KI4Lm7ZsuTUSpECrHiS3uPOjboCigOWGayKzUHUICNrGK0zxgZXhhl6V7y9pImRl34ID/tZhr3veW4pQKgscv6sQjGJzaph2oCP7uZC6arGWcFpc2pgfBcobmOXYPWKskU3eWKClHBJnJ8MoOru+ObOb+izPhINHOmzP26TnKzFxdZiL+onxjadPYslcLtqlmOYpb/5hHgGOvitLhCLHCp0gYNB2uzj0sVxNs3k7k43KrlO5L6gp1KVaIw2a1yZzOCqDWWcePfKM3Mii9JdVyfHZLRRjFCQiOYo41AltHU+9IcaoT4J/j7pKw5tnlu2VaMlnN0dISpoq/ak0m4YjTd3XdRQeH9ktWmclkc65LdLKf9hIqjVqvOhQUJYkuT7OPgr+o7Z9BnClXMz1/CYWftwQE=";

// Sellos de ESCUELA KEMPER URGATE
$escuelaKemperUrgateBase64Cer = "MIIFsDCCA5igAwIBAgIUMzAwMDEwMDAwMDA1MDAwMDM0MTYwDQYJKoZIhvcNAQELBQAwggErMQ8wDQYDVQQDDAZBQyBVQVQxLjAsBgNVBAoMJVNFUlZJQ0lPIERFIEFETUlOSVNUUkFDSU9OIFRSSUJVVEFSSUExGjAYBgNVBAsMEVNBVC1JRVMgQXV0aG9yaXR5MSgwJgYJKoZIhvcNAQkBFhlvc2Nhci5tYXJ0aW5lekBzYXQuZ29iLm14MR0wGwYDVQQJDBQzcmEgY2VycmFkYSBkZSBjYWxpejEOMAwGA1UEEQwFMDYzNzAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBDSVVEQUQgREUgTUVYSUNPMREwDwYDVQQHDAhDT1lPQUNBTjERMA8GA1UELRMIMi41LjQuNDUxJTAjBgkqhkiG9w0BCQITFnJlc3BvbnNhYmxlOiBBQ0RNQS1TQVQwHhcNMjMwNTE4MTE0MzUxWhcNMjcwNTE4MTE0MzUxWjCB1zEnMCUGA1UEAxMeRVNDVUVMQSBLRU1QRVIgVVJHQVRFIFNBIERFIENWMScwJQYDVQQpEx5FU0NVRUxBIEtFTVBFUiBVUkdBVEUgU0EgREUgQ1YxJzAlBgNVBAoTHkVTQ1VFTEEgS0VNUEVSIFVSR0FURSBTQSBERSBDVjElMCMGA1UELRMcRUtVOTAwMzE3M0M5IC8gVkFEQTgwMDkyN0RKMzEeMBwGA1UEBRMVIC8gVkFEQTgwMDkyN0hTUlNSTDA1MRMwEQYDVQQLEwpTdWN1cnNhbCAxMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtmecO6n2GS0zL025gbHGQVxznPDICoXzR2uUngz4DqxVUC/w9cE6FxSiXm2ap8Gcjg7wmcZfm85EBaxCx/0J2u5CqnhzIoGCdhBPuhWQnIh5TLgj/X6uNquwZkKChbNe9aeFirU/JbyN7Egia9oKH9KZUsodiM/pWAH00PCtoKJ9OBcSHMq8Rqa3KKoBcfkg1ZrgueffwRLws9yOcRWLb02sDOPzGIm/jEFicVYt2Hw1qdRE5xmTZ7AGG0UHs+unkGjpCVeJ+BEBn0JPLWVvDKHZAQMj6s5Bku35+d/MyATkpOPsGT/VTnsouxekDfikJD1f7A1ZpJbqDpkJnss3vQIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQsFAAOCAgEAFaUgj5PqgvJigNMgtrdXZnbPfVBbukAbW4OGnUhNrA7SRAAfv2BSGk16PI0nBOr7qF2mItmBnjgEwk+DTv8Zr7w5qp7vleC6dIsZFNJoa6ZndrE/f7KO1CYruLXr5gwEkIyGfJ9NwyIagvHHMszzyHiSZIA850fWtbqtythpAliJ2jF35M5pNS+YTkRB+T6L/c6m00ymN3q9lT1rB03YywxrLreRSFZOSrbwWfg34EJbHfbFXpCSVYdJRfiVdvHnewN0r5fUlPtR9stQHyuqewzdkyb5jTTw02D2cUfL57vlPStBj7SEi3uOWvLrsiDnnCIxRMYJ2UA2ktDKHk+zWnsDmaeleSzonv2CHW42yXYPCvWi88oE1DJNYLNkIjua7MxAnkNZbScNw01A6zbLsZ3y8G6eEYnxSTRfwjd8EP4kdiHNJftm7Z4iRU7HOVh79/lRWB+gd171s3d/mI9kte3MRy6V8MMEMCAnMboGpaooYwgAmwclI2XZCczNWXfhaWe0ZS5PmytD/GDpXzkX0oEgY9K/uYo5V77NdZbGAjmyi8cE2B2ogvyaN2XfIInrZPgEffJ4AB7kFA2mwesdLOCh0BLD9itmCve3A1FGR4+stO2ANUoiI3w3Tv2yQSg4bjeDlJ08lXaaFCLW2peEXMXjQUk7fmpb5MNuOUTW6BE=";
$escuelaKemperUrgateBase64Key = "MIIFDjBABgkqhkiG9w0BBQ0wMzAbBgkqhkiG9w0BBQwwDgQIAgEAAoIBAQACAggAMBQGCCqGSIb3DQMHBAgwggS/AgEAMASCBMh4EHl7aNSCaMDA1VlRoXCZ5UUmqErAbucoZQObOaLUEm+I+QZ7Y8Giupo+F1XWkLvAsdk/uZlJcTfKLJyJbJwsQYbSpLOCLataZ4O5MVnnmMbfG//NKJn9kSMvJQZhSwAwoGLYDm1ESGezrvZabgFJnoQv8Si1nAhVGTk9FkFBesxRzq07dmZYwFCnFSX4xt2fDHs1PMpQbeq83aL/PzLCce3kxbYSB5kQlzGtUYayiYXcu0cVRu228VwBLCD+2wTDDoCmRXtPesgrLKUR4WWWb5N2AqAU1mNDC+UEYsENAerOFXWnmwrcTAu5qyZ7GsBMTpipW4Dbou2yqQ0lpA/aB06n1kz1aL6mNqGPaJ+OqoFuc8Ugdhadd+MmjHfFzoI20SZ3b2geCsUMNCsAd6oXMsZdWm8lzjqCGWHFeol0ik/xHMQvuQkkeCsQ28PBxdnUgf7ZGer+TN+2ZLd2kvTBOk6pIVgy5yC6cZ+o1Tloql9hYGa6rT3xcMbXlW+9e5jM2MWXZliVW3ZhaPjptJFDbIfWxJPjz4QvKyJk0zok4muv13Iiwj2bCyefUTRz6psqI4cGaYm9JpscKO2RCJN8UluYGbbWmYQU+Int6LtZj/lv8p6xnVjWxYI+rBPdtkpfFYRp+MJiXjgPw5B6UGuoruv7+vHjOLHOotRo+RdjZt7NqL9dAJnl1Qb2jfW6+d7NYQSI/bAwxO0sk4taQIT6Gsu/8kfZOPC2xk9rphGqCSS/4q3Os0MMjA1bcJLyoWLp13pqhK6bmiiHw0BBXH4fbEp4xjSbpPx4tHXzbdn8oDsHKZkWh3pPC2J/nVl0k/yF1KDVowVtMDXE47k6TGVcBoqe8PDXCG9+vjRpzIidqNo5qebaUZu6riWMWzldz8x3Z/jLWXuDiM7/Yscn0Z2GIlfoeyz+GwP2eTdOw9EUedHjEQuJY32bq8LICimJ4Ht+zMJKUyhwVQyAER8byzQBwTYmYP5U0wdsyIFitphw+/IH8+v08Ia1iBLPQAeAvRfTTIFLCs8foyUrj5Zv2B/wTYIZy6ioUM+qADeXyo45uBLLqkN90Rf6kiTqDld78NxwsfyR5MxtJLVDFkmf2IMMJHTqSfhbi+7QJaC11OOUJTD0v9wo0X/oO5GvZhe0ZaGHnm9zqTopALuFEAxcaQlc4R81wjC4wrIrqWnbcl2dxiBtD73KW+wcC9ymsLf4I8BEmiN25lx/OUc1IHNyXZJYSFkEfaxCEZWKcnbiyf5sqFSSlEqZLc4lUPJFAoP6s1FHVcyO0odWqdadhRZLZC9RCzQgPlMRtji/OXy5phh7diOBZv5UYp5nb+MZ2NAB/eFXm2JLguxjvEstuvTDmZDUb6Uqv++RdhO5gvKf/AcwU38ifaHQ9uvRuDocYwVxZS2nr9rOwZ8nAh+P2o4e0tEXjxFKQGhxXYkn75H3hhfnFYjik/2qunHBBZfcdG148MaNP6DjX33M238T9Zw/GyGx00JMogr2pdP4JAErv9a5yt4YR41KGf8guSOUbOXVARw6+ybh7+meb7w4BeTlj3aZkv8tVGdfIt3lrwVnlbzhLjeQY6PplKp3/a5Kr5yM0T4wJoKQQ6v3vSNmrhpbuAtKxpMILe8CQoo=";

// Definir la fecha actual
$currentDate = getCurrentDate();

// Crear cliente HTTP
$client = new FiscalApiClient($settings);


try {

    // ==================================================================
    //  NÓMINA ORDINARIA POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina ordinaria por valores
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'expeditionZipCode' => "42501",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'employerData' => [
    //             'employerRegistration' => "B5510768108"
    //         ],
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "FUNK671228PH6",
    //         'legalName' => "KARLA FUENTE NOLASCO",
    //         'zipCode' => "01160",
    //         'taxRegimeCode' => "605",
    //         'cfdiUseCode' => "CN01",
    //         'email' => "someone@somewhere.com",
    //         'employeeData' => [
    //             'curp' => "XEXX010101MNEXXXA8",
    //             'socialSecurityNumber' => "04078873454",
    //             'laborRelationStartDate' => "2024-08-18T00:00:00",
    //             'seniority' => "P54W",
    //             'satContractTypeId' => "01",
    //             'satTaxRegimeTypeId' => "02",
    //             'employeeNumber' => "123456789",
    //             'department' => "GenAI",
    //             'position' => "Sr Software Engineer",
    //             'satJobRiskId' => "1",
    //             'satPaymentPeriodicityId' => "05",
    //             'satBankId' => "012",
    //             'baseSalaryForContributions' => "282.50",
    //             'integratedDailySalary' => "2626.23",
    //             'satPayrollStateId' => "JAL"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2025-08-30T00:00:00",
    //             'initialPaymentDate' => "2025-07-31T00:00:00",
    //             'finalPaymentDate' => "2025-08-30T00:00:00",
    //             'daysPaid' => 30,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "1003",
    //                         'concept' => "Sueldo nominal",
    //                         'taxedAmount' => "95030.00",
    //                         'exemptAmount' => "0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "005",
    //                         'code' => "5913",
    //                         'concept' => "Fondo de Ahorro Aportación Patrón",
    //                         'taxedAmount' => "0",
    //                         'exemptAmount' => "4412.46"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "038",
    //                         'code' => "1885",
    //                         'concept' => "Bono Ingles",
    //                         'taxedAmount' => "14254.50",
    //                         'exemptAmount' => "0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "029",
    //                         'code' => "1941",
    //                         'concept' => "Vales Despensa",
    //                         'taxedAmount' => "0",
    //                         'exemptAmount' => "3439"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "038",
    //                         'code' => "1824",
    //                         'concept' => "Herramientas Teletrabajo (telecom y prop. electri)",
    //                         'taxedAmount' => "273",
    //                         'exemptAmount' => "0"
    //                     ]
    //                 ],
    //                 'otherPayments' => [
    //                     [
    //                         'otherPaymentTypeCode' => "002",
    //                         'code' => "5050",
    //                         'concept' => "exceso de subsidio al empleo",
    //                         'amount' => "0",
    //                         'subsidyCaused' => "0"
    //                     ]
    //                 ]
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "5003",
    //                     'concept' => "ISR Causado",
    //                     'amount' => "27645"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "5910",
    //                     'concept' => "Fondo de ahorro Empleado Inversión",
    //                     'amount' => "4412.46"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "5914",
    //                     'concept' => "Fondo de Ahorro Patrón Inversión",
    //                     'amount' => "4412.46"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "1966",
    //                     'concept' => "Contribución póliza exceso GMM",
    //                     'amount' => "519.91"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "1934",
    //                     'concept' => "Descuento Vales Despensa",
    //                     'amount' => "1"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "1942",
    //                     'concept' => "Vales Despensa Electrónico",
    //                     'amount' => "3439"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "001",
    //                     'code' => "1895",
    //                     'concept' => "IMSS",
    //                     'amount' => "2391.13"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA ASIMILADOS POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina asimilados por valores
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "06880",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'employerData' => [
    //             'originEmployerTin' => "EKU9003173C9"
    //         ],
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "CACX7605101P8",
    //         'legalName' => "XOCHILT CASAS CHAVEZ",
    //         'zipCode' => "36257",
    //         'taxRegimeCode' => "605",
    //         'cfdiUseCode' => "CN01",
    //         'employeeData' => [
    //             'curp' => "XEXX010101HNEXXXA4",
    //             'satContractTypeId' => "09",
    //             'satUnionizedStatusId' => "No",
    //             'satTaxRegimeTypeId' => "09",
    //             'employeeNumber' => "00002",
    //             'department' => "ADMINISTRACION",
    //             'position' => "DIRECTOR DE ADMINISTRACION",
    //             'satPaymentPeriodicityId' => "99",
    //             'satBankId' => "012",
    //             'bankAccount' => "1111111111",
    //             'satPayrollStateId' => "CMX"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "E",
    //             'paymentDate' => "2023-06-02T00:00:00",
    //             'initialPaymentDate' => "2023-06-01T00:00:00",
    //             'finalPaymentDate' => "2023-06-02T00:00:00",
    //             'daysPaid' => 1,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "046",
    //                         'code' => "010046",
    //                         'concept' => "INGRESOS ASIMILADOS A SALARIOS",
    //                         'taxedAmount' => "111197.73",
    //                         'exemptAmount' => "0.00"
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "020002",
    //                     'concept' => "ISR",
    //                     'amount' => "36197.73"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA CON BONOS, FONDO DE AHORRO Y DEDUCCIONES POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina con bonos, fondo de ahorro y deducciones por valores
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'employerData' => [
    //             'employerRegistration' => "Z0000001234"
    //         ],
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "XOJI740919U48",
    //         'legalName' => "INGRID XODAR JIMENEZ",
    //         'zipCode' => "76028",
    //         'taxRegimeCode' => "605",
    //         'cfdiUseCode' => "CN01",
    //         'employeeData' => [
    //             'curp' => "XEXX010101MNEXXXA8",
    //             'socialSecurityNumber' => "0000000000",
    //             'laborRelationStartDate' => "2022-03-02T00:00:00",
    //             'seniority' => "P66W",
    //             'satContractTypeId' => "01",
    //             'satUnionizedStatusId' => "No",
    //             'satTaxRegimeTypeId' => "02",
    //             'employeeNumber' => "111111",
    //             'satJobRiskId' => "4",
    //             'satPaymentPeriodicityId' => "02",
    //             'integratedDailySalary' => "180.96",
    //             'satPayrollStateId' => "GUA"
    //         ]
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "84111505",
    //             'itemSku' => "84111505",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "ACT",
    //             'description' => "Pago de nómina",
    //             'unitPrice' => "1842.82",
    //             'discount' => "608.71",
    //             'taxObjectCode' => "01"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-06-11T00:00:00",
    //             'initialPaymentDate' => "2023-06-05T00:00:00",
    //             'finalPaymentDate' => "2023-06-11T00:00:00",
    //             'daysPaid' => 7,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "SP01",
    //                         'concept' => "SUELDO",
    //                         'taxedAmount' => "1210.30",
    //                         'exemptAmount' => "0.00"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "010",
    //                         'code' => "SP02",
    //                         'concept' => "PREMIO PUNTUALIDAD",
    //                         'taxedAmount' => "121.03",
    //                         'exemptAmount' => "0.00"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "029",
    //                         'code' => "SP03",
    //                         'concept' => "MONEDERO ELECTRONICO",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "269.43"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "010",
    //                         'code' => "SP04",
    //                         'concept' => "PREMIO DE ASISTENCIA",
    //                         'taxedAmount' => "121.03",
    //                         'exemptAmount' => "0.00"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "005",
    //                         'code' => "SP54",
    //                         'concept' => "APORTACION FONDO AHORRO",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "121.03"
    //                     ]
    //                 ],
    //                 'otherPayments' => [
    //                     [
    //                         'otherPaymentTypeCode' => "002",
    //                         'code' => "ISRSUB",
    //                         'concept' => "Subsidio ISR para empleo",
    //                         'amount' => "0.0",
    //                         'subsidyCaused' => "0.0",
    //                         'balanceCompensation' => [
    //                             'favorableBalance' => "0.0",
    //                             'year' => 2022,
    //                             'remainingFavorableBalance' => "0.0"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "ZA09",
    //                     'concept' => "APORTACION FONDO AHORRO",
    //                     'amount' => "121.03"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "ISR",
    //                     'concept' => "ISR",
    //                     'amount' => "36.57"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "001",
    //                     'code' => "IMSS",
    //                     'concept' => "Cuota de Seguridad Social EE",
    //                     'amount' => "30.08"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "ZA68",
    //                     'concept' => "DEDUCCION FDO AHORRO PAT",
    //                     'amount' => "121.03"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "018",
    //                     'code' => "ZA11",
    //                     'concept' => "APORTACION CAJA AHORRO",
    //                     'amount' => "300.00"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA CON HORAS EXTRA POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina con horas extra por valores
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'employerData' => [
    //             'employerRegistration' => "B5510768108",
    //             'originEmployerTin' => "URE180429TM6"
    //         ],
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "XOJI740919U48",
    //         'legalName' => "INGRID XODAR JIMENEZ",
    //         'zipCode' => "76028",
    //         'taxRegimeCode' => "605",
    //         'cfdiUseCode' => "CN01",
    //         'employeeData' => [
    //             'curp' => "XEXX010101HNEXXXA4",
    //             'socialSecurityNumber' => "000000",
    //             'laborRelationStartDate' => "2015-01-01T00:00:00",
    //             'seniority' => "P437W",
    //             'satContractTypeId' => "01",
    //             'satWorkdayTypeId' => "01",
    //             'satTaxRegimeTypeId' => "03",
    //             'employeeNumber' => "120",
    //             'department' => "Desarrollo",
    //             'position' => "Ingeniero de Software",
    //             'satJobRiskId' => "1",
    //             'satPaymentPeriodicityId' => "04",
    //             'satBankId' => "002",
    //             'bankAccount' => "1111111111",
    //             'baseSalaryForContributions' => "490.22",
    //             'integratedDailySalary' => "146.47",
    //             'satPayrollStateId' => "JAL"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-05-24T00:00:00",
    //             'initialPaymentDate' => "2023-05-09T00:00:00",
    //             'finalPaymentDate' => "2023-05-24T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "00500",
    //                         'concept' => "Sueldos, Salarios Rayas y Jornales",
    //                         'taxedAmount' => "2808.8",
    //                         'exemptAmount' => "2191.2"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "019",
    //                         'code' => "00100",
    //                         'concept' => "Horas Extra",
    //                         'taxedAmount' => "50.00",
    //                         'exemptAmount' => "50.00",
    //                         'overtime' => [
    //                             [
    //                                 'days' => 1,
    //                                 'hoursTypeCode' => "01",
    //                                 'extraHours' => 2,
    //                                 'amountPaid' => "100.00"
    //                             ]
    //                         ]
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "001",
    //                     'code' => "00301",
    //                     'concept' => "Seguridad Social",
    //                     'amount' => "200.00"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "00302",
    //                     'concept' => "ISR",
    //                     'amount' => "100"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA CON INCAPACIDADES POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina con incapacidades por valores
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'employerData' => [
    //             'employerRegistration' => "B5510768108",
    //             'originEmployerTin' => "URE180429TM6"
    //         ],
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "XOJI740919U48",
    //         'legalName' => "INGRID XODAR JIMENEZ",
    //         'zipCode' => "76028",
    //         'taxRegimeCode' => "605",
    //         'cfdiUseCode' => "CN01",
    //         'employeeData' => [
    //             'curp' => "XEXX010101HNEXXXA4",
    //             'socialSecurityNumber' => "000000",
    //             'laborRelationStartDate' => "2015-01-01T00:00:00",
    //             'seniority' => "P437W",
    //             'satContractTypeId' => "01",
    //             'satWorkdayTypeId' => "01",
    //             'satTaxRegimeTypeId' => "03",
    //             'employeeNumber' => "120",
    //             'department' => "Desarrollo",
    //             'position' => "Ingeniero de Software",
    //             'satJobRiskId' => "1",
    //             'satPaymentPeriodicityId' => "04",
    //             'satBankId' => "002",
    //             'bankAccount' => "1111111111",
    //             'baseSalaryForContributions' => "490.22",
    //             'integratedDailySalary' => "146.47",
    //             'satPayrollStateId' => "JAL"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-05-24T00:00:00",
    //             'initialPaymentDate' => "2023-05-09T00:00:00",
    //             'finalPaymentDate' => "2023-05-24T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "00500",
    //                         'concept' => "Sueldos, Salarios Rayas y Jornales",
    //                         'taxedAmount' => "2808.80",
    //                         'exemptAmount' => "2191.20"
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "001",
    //                     'code' => "00301",
    //                     'concept' => "Seguridad Social",
    //                     'amount' => "200.00"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "00302",
    //                     'concept' => "ISR",
    //                     'amount' => "100.00"
    //                 ]
    //             ],
    //             'disabilities' => [
    //                 [
    //                     'disabilityDays' => 1,
    //                     'disabilityTypeCode' => "01"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA CON SNCF POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina con SNCF por valores
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "39074",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "OÑO120726RX3",
    //         'legalName' => "ORGANICOS ÑAVEZ OSORIO",
    //         'taxRegimeCode' => "601",
    //         'employerData' => [
    //             'employerRegistration' => "27112029",
    //             'satFundSourceId' => "IP"
    //         ],
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $organicosNavezOsorioBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $organicosNavezOsorioBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "CACX7605101P8",
    //         'legalName' => "XOCHILT CASAS CHAVEZ",
    //         'zipCode' => "36257",
    //         'taxRegimeCode' => "605",
    //         'cfdiUseCode' => "CN01",
    //         'employeeData' => [
    //             'curp' => "XEXX010101HNEXXXA4",
    //             'socialSecurityNumber' => "80997742673",
    //             'laborRelationStartDate' => "2021-09-01T00:00:00",
    //             'seniority' => "P88W",
    //             'satContractTypeId' => "01",
    //             'satTaxRegimeTypeId' => "02",
    //             'employeeNumber' => "273",
    //             'satJobRiskId' => "1",
    //             'satPaymentPeriodicityId' => "04",
    //             'integratedDailySalary' => "221.48",
    //             'satPayrollStateId' => "GRO"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-05-16T00:00:00",
    //             'initialPaymentDate' => "2023-05-01T00:00:00",
    //             'finalPaymentDate' => "2023-05-16T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "P001",
    //                         'concept' => "Sueldos, Salarios Rayas y Jornales",
    //                         'taxedAmount' => "3322.20",
    //                         'exemptAmount' => "0.0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "038",
    //                         'code' => "P540",
    //                         'concept' => "Compensacion",
    //                         'taxedAmount' => "100.0",
    //                         'exemptAmount' => "0.0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "038",
    //                         'code' => "P550",
    //                         'concept' => "Compensación Garantizada Extraordinaria",
    //                         'taxedAmount' => "2200.0",
    //                         'exemptAmount' => "0.0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "038",
    //                         'code' => "P530",
    //                         'concept' => "Servicio Extraordinario",
    //                         'taxedAmount' => "200.0",
    //                         'exemptAmount' => "0.0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "P506",
    //                         'concept' => "Otras Prestaciones",
    //                         'taxedAmount' => "1500.0",
    //                         'exemptAmount' => "0.0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "P505",
    //                         'concept' => "Remuneración al Desempeño Legislativo",
    //                         'taxedAmount' => "17500.0",
    //                         'exemptAmount' => "0.0"
    //                     ]
    //                 ],
    //                 'otherPayments' => [
    //                     [
    //                         'otherPaymentTypeCode' => "002",
    //                         'code' => "o002",
    //                         'concept' => "Subsidio para el empleo efectivamente entregado al trabajador",
    //                         'amount' => "0.0",
    //                         'subsidyCaused' => "0.0"
    //                     ]
    //                 ]
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "D002",
    //                     'concept' => "ISR",
    //                     'amount' => "4716.61"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "D525",
    //                     'concept' => "Redondeo",
    //                     'amount' => "0.81"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "001",
    //                     'code' => "D510",
    //                     'concept' => "Cuota Trabajador ISSSTE",
    //                     'amount' => "126.78"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA EXTRAORDINARIA (AGUINALDO) POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina extraordinaria (aguinaldo) por valores
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'employerData' => [
    //             'employerRegistration' => "B5510768108",
    //             'originEmployerTin' => "URE180429TM6"
    //         ],
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "XOJI740919U48",
    //         'legalName' => "INGRID XODAR JIMENEZ",
    //         'zipCode' => "76028",
    //         'taxRegimeCode' => "605",
    //         'cfdiUseCode' => "CN01",
    //         'employeeData' => [
    //             'curp' => "XEXX010101HNEXXXA4",
    //             'socialSecurityNumber' => "000000",
    //             'laborRelationStartDate' => "2015-01-01T00:00:00",
    //             'seniority' => "P439W",
    //             'satContractTypeId' => "01",
    //             'satWorkdayTypeId' => "01",
    //             'satTaxRegimeTypeId' => "03",
    //             'employeeNumber' => "120",
    //             'department' => "Desarrollo",
    //             'position' => "Ingeniero de Software",
    //             'satJobRiskId' => "1",
    //             'satPaymentPeriodicityId' => "99",
    //             'satBankId' => "002",
    //             'bankAccount' => "1111111111",
    //             'integratedDailySalary' => "146.47",
    //             'satPayrollStateId' => "JAL"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "E",
    //             'paymentDate' => "2023-06-04T00:00:00",
    //             'initialPaymentDate' => "2023-06-04T00:00:00",
    //             'finalPaymentDate' => "2023-06-04T00:00:00",
    //             'daysPaid' => 30,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "002",
    //                         'code' => "00500",
    //                         'concept' => "Gratificación Anual (Aguinaldo)",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "10000.00"
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => []
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA SEPARACIÓN INDEMNIZACIÓN POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina separación indemnización por valores
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'employerData' => [
    //             'employerRegistration' => "B5510768108",
    //             'originEmployerTin' => "URE180429TM6"
    //         ],
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "XOJI740919U48",
    //         'legalName' => "INGRID XODAR JIMENEZ",
    //         'zipCode' => "76028",
    //         'taxRegimeCode' => "605",
    //         'cfdiUseCode' => "CN01",
    //         'employeeData' => [
    //             'curp' => "XEXX010101HNEXXXA4",
    //             'socialSecurityNumber' => "000000",
    //             'laborRelationStartDate' => "2015-01-01T00:00:00",
    //             'seniority' => "P439W",
    //             'satContractTypeId' => "01",
    //             'satWorkdayTypeId' => "01",
    //             'satTaxRegimeTypeId' => "03",
    //             'employeeNumber' => "120",
    //             'department' => "Desarrollo",
    //             'position' => "Ingeniero de Software",
    //             'satJobRiskId' => "1",
    //             'satPaymentPeriodicityId' => "99",
    //             'satBankId' => "002",
    //             'bankAccount' => "1111111111",
    //             'integratedDailySalary' => "146.47",
    //             'satPayrollStateId' => "JAL"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "E",
    //             'paymentDate' => "2023-06-04T00:00:00",
    //             'initialPaymentDate' => "2023-05-05T00:00:00",
    //             'finalPaymentDate' => "2023-06-04T00:00:00",
    //             'daysPaid' => 30,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "023",
    //                         'code' => "00500",
    //                         'concept' => "Pagos por separación",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "10000.00"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "025",
    //                         'code' => "00900",
    //                         'concept' => "Indemnizaciones",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "500.00"
    //                     ]
    //                 ],
    //                 'otherPayments' => [],
    //                 'severance' => [
    //                     'totalPaid' => "10500.00",
    //                     'yearsOfService' => 1,
    //                     'lastMonthlySalary' => "10000.00",
    //                     'accumulableIncome' => "10000.00",
    //                     'nonAccumulableIncome' => "0.00"
    //                 ]
    //             ],
    //             'deductions' => []
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA JUBILACIÓN POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina jubilación por valores
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'employerData' => [
    //             'employerRegistration' => "B5510768108",
    //             'originEmployerTin' => "URE180429TM6"
    //         ],
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "XOJI740919U48",
    //         'legalName' => "INGRID XODAR JIMENEZ",
    //         'zipCode' => "76028",
    //         'taxRegimeCode' => "605",
    //         'cfdiUseCode' => "CN01",
    //         'employeeData' => [
    //             'curp' => "XEXX010101HNEXXXA4",
    //             'socialSecurityNumber' => "000000",
    //             'laborRelationStartDate' => "2015-01-01T00:00:00",
    //             'seniority' => "P439W",
    //             'satContractTypeId' => "01",
    //             'satWorkdayTypeId' => "01",
    //             'satTaxRegimeTypeId' => "03",
    //             'employeeNumber' => "120",
    //             'department' => "Desarrollo",
    //             'position' => "Ingeniero de Software",
    //             'satJobRiskId' => "1",
    //             'satPaymentPeriodicityId' => "99",
    //             'satBankId' => "002",
    //             'bankAccount' => "1111111111",
    //             'integratedDailySalary' => "146.47",
    //             'satPayrollStateId' => "JAL"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "E",
    //             'paymentDate' => "2023-05-05T00:00:00",
    //             'initialPaymentDate' => "2023-06-04T00:00:00",
    //             'finalPaymentDate' => "2023-06-04T00:00:00",
    //             'daysPaid' => 30,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "039",
    //                         'code' => "00500",
    //                         'concept' => "Jubilaciones, pensiones o haberes de retiro",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "10000.00"
    //                     ]
    //                 ],
    //                 'otherPayments' => [],
    //                 'retirement' => [
    //                     'totalOneTime' => "10000.00",
    //                     'accumulableIncome' => "10000.00",
    //                     'nonAccumulableIncome' => "0.00"
    //                 ]
    //             ],
    //             'deductions' => []
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA SIN DEDUCCIONES POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina sin deducciones por valores
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'employerData' => [
    //             'employerRegistration' => "B5510768108",
    //             'originEmployerTin' => "URE180429TM6"
    //         ],
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "XOJI740919U48",
    //         'legalName' => "INGRID XODAR JIMENEZ",
    //         'zipCode' => "76028",
    //         'taxRegimeCode' => "605",
    //         'cfdiUseCode' => "CN01",
    //         'employeeData' => [
    //             'curp' => "XEXX010101HNEXXXA4",
    //             'socialSecurityNumber' => "000000",
    //             'laborRelationStartDate' => "2015-01-01T00:00:00",
    //             'seniority' => "P437W",
    //             'satContractTypeId' => "01",
    //             'satWorkdayTypeId' => "01",
    //             'satTaxRegimeTypeId' => "03",
    //             'employeeNumber' => "120",
    //             'department' => "Desarrollo",
    //             'position' => "Ingeniero de Software",
    //             'satJobRiskId' => "1",
    //             'satPaymentPeriodicityId' => "04",
    //             'satBankId' => "002",
    //             'bankAccount' => "1111111111",
    //             'baseSalaryForContributions' => "490.22",
    //             'integratedDailySalary' => "146.47",
    //             'satPayrollStateId' => "JAL"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-05-24T00:00:00",
    //             'initialPaymentDate' => "2023-05-09T00:00:00",
    //             'finalPaymentDate' => "2023-05-24T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "00500",
    //                         'concept' => "Sueldos, Salarios Rayas y Jornales",
    //                         'taxedAmount' => "2808.8",
    //                         'exemptAmount' => "2191.2"
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => []
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA VIÁTICOS POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina viáticos por valores
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'employerData' => [
    //             'employerRegistration' => "B5510768108",
    //             'originEmployerTin' => "URE180429TM6"
    //         ],
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "XOJI740919U48",
    //         'legalName' => "INGRID XODAR JIMENEZ",
    //         'zipCode' => "76028",
    //         'taxRegimeCode' => "605",
    //         'cfdiUseCode' => "CN01",
    //         'employeeData' => [
    //             'curp' => "XEXX010101HNEXXXA4",
    //             'socialSecurityNumber' => "000000",
    //             'laborRelationStartDate' => "2015-01-01T00:00:00",
    //             'seniority' => "P438W",
    //             'satContractTypeId' => "01",
    //             'satWorkdayTypeId' => "01",
    //             'satTaxRegimeTypeId' => "03",
    //             'employeeNumber' => "120",
    //             'department' => "Desarrollo",
    //             'position' => "Ingeniero de Software",
    //             'satJobRiskId' => "1",
    //             'satPaymentPeriodicityId' => "04",
    //             'satBankId' => "002",
    //             'bankAccount' => "1111111111",
    //             'baseSalaryForContributions' => "490.22",
    //             'integratedDailySalary' => "146.47",
    //             'satPayrollStateId' => "JAL"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-09-26T00:00:00",
    //             'initialPaymentDate' => "2023-09-11T00:00:00",
    //             'finalPaymentDate' => "2023-09-26T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "050",
    //                         'code' => "050",
    //                         'concept' => "Viaticos",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "3000.00"
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "081",
    //                     'code' => "081",
    //                     'concept' => "Ajuste en viaticos entregados al trabajador",
    //                     'amount' => "3000.00"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA SUBSIDIO CAUSADO AL EMPLEO POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina subsidio causado al empleo por valores
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'employerData' => [
    //             'employerRegistration' => "B5510768108",
    //             'originEmployerTin' => "URE180429TM6"
    //         ],
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "XOJI740919U48",
    //         'legalName' => "INGRID XODAR JIMENEZ",
    //         'zipCode' => "76028",
    //         'taxRegimeCode' => "605",
    //         'cfdiUseCode' => "CN01",
    //         'employeeData' => [
    //             'curp' => "XEXX010101HNEXXXA4",
    //             'socialSecurityNumber' => "000000",
    //             'laborRelationStartDate' => "2015-01-01T00:00:00",
    //             'seniority' => "P437W",
    //             'satContractTypeId' => "01",
    //             'satWorkdayTypeId' => "01",
    //             'satTaxRegimeTypeId' => "02",
    //             'employeeNumber' => "120",
    //             'department' => "Desarrollo",
    //             'position' => "Ingeniero de Software",
    //             'satJobRiskId' => "1",
    //             'satPaymentPeriodicityId' => "04",
    //             'satBankId' => "002",
    //             'bankAccount' => "1111111111",
    //             'baseSalaryForContributions' => "490.22",
    //             'integratedDailySalary' => "146.47",
    //             'satPayrollStateId' => "JAL"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-05-24T00:00:00",
    //             'initialPaymentDate' => "2023-05-09T00:00:00",
    //             'finalPaymentDate' => "2023-05-24T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "00500",
    //                         'concept' => "Sueldos, Salarios Rayas y Jornales",
    //                         'taxedAmount' => "2808.8",
    //                         'exemptAmount' => "2191.2"
    //                     ]
    //                 ],
    //                 'otherPayments' => [
    //                     [
    //                         'otherPaymentTypeCode' => "007",
    //                         'code' => "0002",
    //                         'concept' => "ISR ajustado por subsidio",
    //                         'amount' => "145.80",
    //                         'subsidyCaused' => "0.0"
    //                     ]
    //                 ]
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "107",
    //                     'code' => "D002",
    //                     'concept' => "Ajuste al Subsidio Causado",
    //                     'amount' => "160.35"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "D002",
    //                     'concept' => "ISR",
    //                     'amount' => "145.80"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA BÁSICA POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina básica por valores
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'employerData' => [
    //             'employerRegistration' => "B5510768108",
    //             'originEmployerTin' => "URE180429TM6"
    //         ],
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "XOJI740919U48",
    //         'legalName' => "INGRID XODAR JIMENEZ",
    //         'zipCode' => "76028",
    //         'taxRegimeCode' => "605",
    //         'cfdiUseCode' => "CN01",
    //         'employeeData' => [
    //             'curp' => "XEXX010101HNEXXXA4",
    //             'socialSecurityNumber' => "000000",
    //             'laborRelationStartDate' => "2015-01-01T00:00:00",
    //             'seniority' => "P437W",
    //             'satContractTypeId' => "01",
    //             'satWorkdayTypeId' => "01",
    //             'satTaxRegimeTypeId' => "03",
    //             'employeeNumber' => "120",
    //             'department' => "Desarrollo",
    //             'position' => "Ingeniero de Software",
    //             'satJobRiskId' => "1",
    //             'satPaymentPeriodicityId' => "04",
    //             'satBankId' => "002",
    //             'bankAccount' => "1111111111",
    //             'baseSalaryForContributions' => "490.22",
    //             'integratedDailySalary' => "146.47",
    //             'satPayrollStateId' => "JAL"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-05-24T00:00:00",
    //             'initialPaymentDate' => "2023-05-09T00:00:00",
    //             'finalPaymentDate' => "2023-05-24T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "00500",
    //                         'concept' => "Sueldos, Salarios Rayas y Jornales",
    //                         'taxedAmount' => "2808.8",
    //                         'exemptAmount' => "2191.2"
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "001",
    //                     'code' => "00301",
    //                     'concept' => "Seguridad Social",
    //                     'amount' => "200"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "00302",
    //                     'concept' => "ISR",
    //                     'amount' => "100"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


} catch (\Exception $e) {
    consoleError($e);
}

function consoleLog(FiscalApiHttpResponseInterface $apiResponse): void
{
    echo "apiResponse:\n" . json_encode($apiResponse->getJson(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

function consoleError(\Exception $e): void
{
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

/**
 * Obtiene la fecha actual en formato ISO 8601 sin zona horaria.
 */
function getCurrentDate(): string
{
    return (new \DateTime())->format('Y-m-d\TH:i:s');
}
