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
*
* Data de Criação: 01/08/2006

* @author Desenvolvedor: Cassiano Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/
?>
<body bgcolor=e7ebe7>
<table width=100% height=100% border=0>
    <tr>
        <td align=center>
            <table width=550 height=200 border=0 cellpadding=5 cellspacing=0>
                <tr>
                    <td height=10 bgcolor=515551 align=center>
                        <font FACE="sans-serif, Arial, Helvetica, Geneva" color=ffffff size=4>
                            <b>Parâmetros no PHP.INI Inválidos!</b>
                        </font>
                    </td>
                </tr>
<!--                <tr><td height=1></td></tr>-->
                <tr>
                    <td height=20 bgcolor=b6bab6>
                        <BR>
                        <font FACE="sans-serif, Arial, Helvetica, Geneva" color=000000 size=2>
                            Verifique os valores dos seguintes parâmetros no arquivo php.ini.<BR><BR>

                            <?=$stErroPHPIni;?><BR><BR>
                        </font>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
