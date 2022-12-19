Para que inspector quede bien instalado debes crear una carpeta "private" en la carpeta inspector:
$ mkdir -p carpeta_inspector/private

y en esta carpeta debes crear un archivo llamado config.php con la siguiente información:

================ Contenido de config.php =========================
```php
;<?php die()?>

rutarespaldo = http://ruta_respaldo
ruta = /ruta_absoluta_de_donde_está_instalado_inspector

timezone    = 'America/Caracas'
locale      = 'es_VE'
name        = Inspector
sesion_name = name_of_session

;;;;;;;;;;;;;;;;; Datos de la bd del mbilling
[bd_mb]
bd    = mbilling_bd
login = login_mbilling_bd
pass  = passwd_mbilling_bd
host  = host_mbilling_bd

; Datos de la bd para inspector
[bd_insp]
bd    = inspector_bd
login = login_inspector_bd
pass  = passwd_inspector_bd
host  = host_inspector_bd

;;;;;;;;;;;;;;;;; Operadoras
[op]
0[nombre]          = Nombre_de_Operadora_1
0[prefijo]         = 412
0[prefijo_pais]    = 58
0[n_llamadas]      = 10
0[min_sessiontime] = 15
0[porc_origenes]   = 0.3
0[f3_interv_min]   = 1
0[f3_interv_max]   = 2
0[f5_porc_lim]     = 30
0[f5_t_max]        = 90
0[f6_porc4]        = 30
0[peso_f1]         = 40
0[peso_f2]         = 10
0[peso_f3]         = 20
0[peso_f4_1]       = 10
0[peso_f4_2]       = 20
0[peso_f5_m]       = -20
0[peso_f5_p]       = 10
0[peso_f6_4]       = 20
0[factor_limite]   = 60.0
;;;;
1[nombre]          = Nombre_Operadora_2
1[prefijo]         = 414,424
1[prefijo_pais]    = 58
1[n_llamadas]      = 10
1[min_sessiontime] = 15
1[porc_origenes]   = 0.3
1[f3_interv_min]   = 1
1[f3_interv_max]   = 2
1[f5_porc_lim]     = 30
1[f5_t_max]        = 90
1[f6_porc4]        = 30
1[peso_f1]         = 40
1[peso_f2]         = 10
1[peso_f3]         = 20
1[peso_f4_1]       = 10
1[peso_f4_2]       = 20
1[peso_f5_m]       = -20
1[peso_f5_p]       = 10
1[peso_f6_4]       = 20
1[factor_limite]   = 60.0
;;;;;;;;;;;;;;;;;;;;;;;;;;;;

;;;;;;;;;;;;;;;;;;;;filtros mb
[fmb]
0[nombre]= Filtro1
0[prefijos]=81
0[operadora]=Nombre_Operadora_1
0[tipo] = LB
0[bd]    = mbilling_cta1
0[login] = login_mbilling_cta1
0[pass]  = passwd_mbilling_cta1
0[host]  = host_mbilling_cta1
;;;;
1[nombre]= Filtro2
1[prefijos]=81
1[operadora]=Nombre_Operadora_2
1[tipo] = LN
1[bd]    = mbilling_cta2
1[login] = login_mbilling_cta2
1[pass]  = passwd_mbilling_cta2
1[host]  = host_mbilling_cta2
;;;;
```
