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
* Arquivo instância para popup de Centro de Custo
* Data de Criação: 07/03/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Diego Barbosa Victoria

$Revision: 20971 $
$Name$
$Author: tonismar $
$Date: 2007-03-13 17:38:31 -0300 (Ter, 13 Mar 2007) $

* Casos de uso: uc-03.03.07
uc-03.04.01
*/

/*
$Log$
Revision 1.9  2007/03/13 20:38:31  tonismar
bug #8699

Revision 1.8  2007/02/26 18:12:04  tonismar
bug #8445

Revision 1.7  2007/02/01 10:48:02  hboaventura
Bugs #8237# #8236# #8230# #8229# #8144# #8151# #7786# #8225#

Revision 1.6  2006/12/19 10:46:45  hboaventura
Bug #7629#

Revision 1.5  2006/12/12 12:44:24  rodrigo
Correção da formatação do valor do saldo

Revision 1.4  2006/11/29 19:45:52  larocca
Bug #7629#

Revision 1.3  2006/11/17 10:44:39  rodrigo
Bug 7384

Revision 1.2  2006/11/13 20:25:58  rodrigo
*** empty log message ***

Revision 1.1  2006/09/27 15:39:32  rodrigo
*** empty log message ***

Revision 1.5  2006/07/10 19:40:16  rodrigo
Adicionado nos componentes de itens,marca e centro de custa a função ajax para manipulação dos dados.

Revision 1.4  2006/07/06 14:05:39  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:10:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPermissaoCentroDeCustos.class.php");
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCentroDeCustos.class.php");
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoEstoqueItem.class.php");

$stCampoCod       = $_GET['stNomCampoCod'       ];
$stCampoDesc      = $_GET['stIdCampoDesc'       ];
$inCodigo         = $_REQUEST[ 'inCodigo'       ];
$inCodCentroCusto = $request->get("inCodCentroCusto");

//sistemaLegado::mostraVar($_REQUEST);
switch ($_GET['stCtrl']) {

    case 'buscaPopup':
    default:
        $boErro = true;
        $usuario = isset($usuario) ? $usuario : "";
        $stJs = isset($stJs) ? $stJs : "";
        if ( (trim($inCodigo) != '') AND ($inCodigo != 0) ) {
            if ($usuario) {
                $obRegra = new RAlmoxarifadoPermissaoCentroDeCustos();
                $obRegra->addCentroDeCustos();
                $obRegra->roUltimoCentro->setCodigo( $inCodigo );
                $obRegra->obRCGMPessoaFisica->setNumCGM( Sessao::read('numCgm') );
                $obRegra->listar($rsCentroCusto);
                $stDescricao = $rsCentroCusto->getCampo('descricao');
            } else {
                $obRegra = new RAlmoxarifadoCentroDeCustos();
                $obRegra->setCodigo( $inCodigo );
                $obRegra->consultar();
                $stDescricao = $obRegra->getDescricao();
            }
            $stJs.="d.getElementById('".$stCampoDesc."').innerHTML='".$stDescricao."';";
            $stJs.="retornaValorBscInner('".$stCampoCod."','".$stCampoDesc."','".$_GET['stNomForm']."','".$stDescricao."');";
            if ($stDescricao=="") {
                $stJs.="alertaAviso('@Código do Centro de Custo(".$inCodigo.") não encontrado.','form','erro','".Sessao::getId()."');";
                $boErro = false;
            }

        } elseif (trim($inCodCentroCusto) != '') {
            $obRegra = new RAlmoxarifadoCentroDeCustos();
            $obRegra->setCodigo( $inCodCentroCusto );
            $obRegra->consultar();
            $stDescricao = $obRegra->getDescricao();
            $stJs.="d.getElementById('".$stCampoDesc."').innerHTML = '".$stDescricao."';";
            $stJs.="retornaValorBscInner('".$stCampoCod."','".$stCampoDesc."','".$_GET['stNomForm']."','".$stDescricao."');";
            if ($stDescricao=="") {
                $stJs.="alertaAviso('@Código do Centro de Custo(".$inCodCentroCusto.") não encontrado.','form','erro','".Sessao::getId()."');";
                $boErro = false;
            }
        } else {
            $stJs.="alertaAviso('@Código do Centro de Custo(".$inCodCentroCusto.") não encontrado.','form','erro','".Sessao::getId()."');";
            $stJs.="f.".$stCampoCod.".value = '';\n";
            $stJs.="d.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
            $boErro = false;
        }
        /*
        if ($boErro) {
            $obEstoqueItem = new RAlmoxarifadoEstoqueItem();
            $obEstoqueItem->obRCentroDeCustos->setCodigo($inCodigo);
            if (isset($_REQUEST['inCodItem'])) {
                $obEstoqueItem->obRCatalogoItem->setCodigo($_REQUEST['inCodItem']);
            }
            $obEstoqueItem->retornaSaldoEstoque( $inSaldoEstoque );
            ($inSaldoEstoque=="") ? $inSaldoEstoque = '0,00' : $inSaldoEstoque = number_format($inSaldoEstoque,2,',','.');
            //if ($obEstoqueItem->obRCatalogoItem->getCodigo()!="") {
                //$stJs.=" if (d.getElementById('lblSaldoEstoque')) {                              ";
                    $stJs.="  d.getElementById('lblSaldoEstoque').innerHTML='".number_format($inSaldoEstoque,4,',','.')."'; ";
                $stJs.="	var inSaldoEstoque;
                    inSaldoEstoque = '".$inSaldoEstoque."';";
                $stJs.="  d.getElementById('lblSaldoEstoque').innerHTML=inSaldoEstoque;";
                //$stJs.=" }                                                                     ";
            //}
        } else {
            $stJs .= "f.inCodCentroCusto.value                       = '';      ";
            $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
            $stJs.="  d.getElementById('lblSaldoEstoque').innerHTML = '&nbsp;';";
        }*/
        echo $stJs;
        //SistemaLegado::executaFrameOculto($stJs);
        break;

}

?>
