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
<?
/**
    * Funcoes Java Script para Consulta de Ordens de Pagamento
    * Data de Criação: 30/05/2005
    
    
    * @author Analista: Dieine
    * @author Desenvolvedor: Marcelo B. Paulino
    
    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
    
    * Casos de uso: uc-02.01.24
                    uc-02.03.05
*/

/*
$Log$
Revision 1.3  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/
?>

<script type="text/javascript">

function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    stAction = document.frm.action;
    stTarget = document.frm.target;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target = 'oculto';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function Limpar() {
    var d = document;
    var f = d.frm;
    d.getElementById( "stNomTipoNorma" ).innerHTML = "&nbsp;";
    d.getElementById( "stNomDotacaoOrcamentaria" ).innerHTML = "&nbsp;";
    f.inCodNorma.value = '';
}
                                    
</script>
                
