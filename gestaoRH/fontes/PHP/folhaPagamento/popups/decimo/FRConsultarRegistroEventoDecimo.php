<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
* Página de Frames da Consultar Registros de Eventos de Décimo Terceiro
* Data de Criação: 09/09/2006

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30547 $
$Name$
$Author: souzadl $
$Date: 2006-09-08 07:05:52 -0300 (Sex, 08 Set 2006) $

* Casos de uso: uc-04.05.21
*/

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<script type="text/javascript">
      window.status = ":::::::: URBEM ::::::::";
</script>
<html>
<head>
    <title>URBEM :: Consultar Registros de Eventos</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<frameset rows="*,0" border="0" noresize >
    <frame name="telaPrincipal" src="./<?=$_GET["sUrlConsulta"]."&iURLRandomica=".$_GET["iURLRandomica"]."&inContrato=".$_GET["inContrato"]."&inCodMes=".$_GET["inCodMes"]."&inAno=".$_GET["inAno"]?>" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0" noresize >
    <frame name="oculto" src="" marginwidth="100%" marginheight="100%" scrolling="yes" frameborder="1" noresize >
</frameset>

</html>
