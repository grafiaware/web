[16/Mar/2021 18:24:38][6376] {smtps} Task 75916 handler BEGIN
[16/Mar/2021 18:24:42][6376] {conn} Connection from 192.168.10.129:55344 to 192.168.32.10:25, socket 43960.
[16/Mar/2021 18:24:42][6376] {smtps} Task 75916 handler starting
[16/Mar/2021 18:24:42][6376] {smtps} SMTP server session begin; client connected from NB-SVOBODA:55344
[16/Mar/2021 18:24:42][6376] {smtps} Sent SMTP greeting to NB-SVOBODA:55344
[16/Mar/2021 18:24:42][6376] {smtps} Command EHLO localhost
[16/Mar/2021 18:24:42][6376] {smtps} Sent reply to EHLO: 250 posta.grafia.cz ...
[16/Mar/2021 18:24:42][6376] {smtps} Command STARTTLS
[16/Mar/2021 18:24:42][6376] {conn} Connection from 192.168.10.129:55344 to 192.168.32.10:25, socket 43960.
[16/Mar/2021 18:24:42][6376] {conn} SSL debug: id 0000000003E35DC0 SSL handshake started: before/accept initialization
[16/Mar/2021 18:24:42][6376] {conn} SSL debug: id 0000000003E35DC0 SSL_accept:before/accept initialization
[16/Mar/2021 18:24:42][6376] {conn} SSL debug: id 0000000003E35DC0 SSL_accept:error in SSLv2/v3 read client hello A
[16/Mar/2021 18:24:43][6376] {conn} SSL debug: id 0000000003E35DC0 Client requests SMTP server by name: posta.grafia.cz
[16/Mar/2021 18:24:43][6376] {conn} SSL debug: id 0000000003E35DC0 Used default SMTP server context for connection by name: posta.grafia.cz
[16/Mar/2021 18:24:43][6376] {conn} SSL debug: id 0000000003E35DC0 SSL_accept:SSLv3 read client hello A
[16/Mar/2021 18:24:43][6376] {conn} SSL debug: id 0000000003E35DC0 SSL_accept:SSLv3 write server hello A
[16/Mar/2021 18:24:43][6376] {conn} SSL debug: id 0000000003E35DC0 SSL_accept:SSLv3 write certificate A
[16/Mar/2021 18:24:43][6376] {conn} SSL debug: id 0000000003E35DC0 SSL_accept:SSLv3 write key exchange A
[16/Mar/2021 18:24:43][6376] {conn} SSL debug: id 0000000003E35DC0 SSL_accept:SSLv3 write server done A
[16/Mar/2021 18:24:43][6376] {conn} SSL debug: id 0000000003E35DC0 SSL_accept:SSLv3 flush data
[16/Mar/2021 18:24:43][6376] {conn} SSL debug: id 0000000003E35DC0 SSL_accept:error in SSLv3 read client certificate A
[16/Mar/2021 18:24:43][6376] {conn} SSL debug: id 0000000003E35DC0 SSL_accept:error in SSLv3 read client certificate A
[16/Mar/2021 18:24:43][6376] {conn} SSL debug: id 0000000003E35DC0 SSL3 alert read:fatal:unknown CA
[16/Mar/2021 18:24:43][6376] {conn} SSL debug: id 0000000003E35DC0 SSL_accept:failed in SSLv3 read client certificate A
[16/Mar/2021 18:24:43][6376] {conn} Cannot accept SSL connection from 192.168.10.129:55344 to 192.168.32.10:25: SSL code 1, system error: (0) Operace byla dokončena úspěšně.
[16/Mar/2021 18:24:43][6376] {conn} SSL error stack: 6376:error:14094418:SSL routines:SSL3_READ_BYTES:tlsv1 alert unknown ca:.\ssl\s3_pkt.c:1300:SSL alert number 48
[16/Mar/2021 18:24:43][6376] {smtps} Failed STARTTLS in SMTP connection with NB-SVOBODA
[16/Mar/2021 18:24:43][6376] {smtps} SMTP server session end
[16/Mar/2021 18:24:43][6376] {conn} Closing socket 43960
[16/Mar/2021 18:24:43][6376] {smtps} Task 75916 handler END