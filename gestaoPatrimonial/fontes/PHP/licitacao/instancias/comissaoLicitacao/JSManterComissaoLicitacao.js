<script type="text/javascript">
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
</script>
<?php
 /**
    * Arquivo JavaScript - 
    * Data de Criação   : 29/11/2006


    * @author Analista:
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 18364 $
    $Name$
    $Autor: $
    $Date: 2006-11-29 15:53:05 -0200 (Qua, 29 Nov 2006) $

    * Casos de uso: uc-03.05.09
*/

/*
$Log$
Revision 1.1  2006/11/29 17:53:05  tonismar
bug #7649


*/
?>
<script type="text/javascript">

function Salvar(){
    document.frm.target = 'telaPrincipal';
    window.location = '<?=$pgList;?>?<?=Sessao::getId();?>&stAcao=<?=$stAcao;?>&pg=<?=$_GET["pg"]?>&pos=<?=$_GET["pos"]?>';
}

</script>

