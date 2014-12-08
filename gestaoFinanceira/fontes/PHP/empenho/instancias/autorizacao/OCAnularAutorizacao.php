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
    * Classe Oculta de Anular Autorização
    * Data de Criação   : 01/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2007-02-23 13:15:05 -0200 (Sex, 23 Fev 2007) $

    * Casos de uso: uc-02.03.02
                    uc-02.01.08
*/

/*
$Log$
Revision 1.5  2007/02/23 15:15:05  gelson
Sempre que for autorização tem que ir a reserva. Adicionado em todos arquivos o caso de uso da reserva.

Revision 1.4  2006/07/05 20:47:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OCAnularAutorizacao.php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obREmpenhoAutorizacaoEmpenho = new REmpenhoPreEmpenho;
$obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );

function montaLista($arRecordSet , $boExecuta = true)
{
        for($i=0;$i<count($arRecordSet);$i++){
                if(isset($arRecordSet[$i]['cod_item'])&&$arRecordSet[$i]['cod_item']!='')
                    $codItem = true;
                break;
        }
        
        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $rsLista->addFormatacao( "vl_total", "NUMERIC_BR" );
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Descrição ");
        $obLista->ultimoCabecalho->setWidth( 50 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor Unitário ");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Quantidade ");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor Total");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        if ($_REQUEST['stAcao'] != 'anular') {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
        }

        $obLista->addDado();
        if ($codItem)
                $obLista->ultimoDado->setCampo( "[cod_item] - [nom_item]" );
        else
                $obLista->ultimoDado->setCampo( "nom_item" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_unitario" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "quantidade" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_total" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        if ($_REQUEST['stAcao'] != 'anular') {
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "EXCLUIR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:excluirItem('excluirItemPreEmpenho');" );
            $obLista->ultimaAcao->addCampo("1","num_item");
            $obLista->commitAcao();
        }

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        foreach ($arRecordSet as $value) {
            $vl_total = str_replace('.','',$value['vl_total']);
            $vl_total = str_replace(',','.',$vl_total);
            $nuVlTotal += $value['vl_total'];
        }
        $nuVlTotal = number_format($nuVlTotal,2,',','.');

        $stLista    = "d.getElementById('spnLista').innerHTML = '".$stHTML."'; ";
        $stVlTotal  = "d.getElementById('nuValorTotal').innerHTML='".$nuVlTotal."'; ";
        $stVlTotal .= "d.getElementById('nuVlReserva').innerHTML= '".$nuVlTotal."'; ";
        $stVlTotal .= "d.getElementById('hdnVlReserva').value= '".$nuVlTotal."'; ";

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto($stLista.$stVlTotal);
        } else {
            return $stLista.$stVlTotal;
        }

}

switch ($stCtrl) {

    case 'montaListaItemPreEmpenhoAnular':
        $js  = montaLista( Sessao::read('arItens'), false );
        SistemaLegado::executaFrameOculto($js);
    break;

}
?>
