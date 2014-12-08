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
    * Página Oculta de Parâmetros do Arquivo de Relacionamento das Despesas
    * Data de Criação   : 11/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.10
*/

/*
$Log$
Revision 1.6  2006/07/05 20:46:20  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EXP_NEGOCIO."RExportacaoTCERJRelDespesa.class.php" );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new RExportacaoTCERJRelDespesa();
$obRegra->obRExportacaoTCERJContaDespesa->setExercicio      ( Sessao::getExercicio() );
$obRegra->obRExportacaoTCERJContaDespesa->listarContas      ( $rsContas ) ;

// Acoes por pagina
switch ($stCtrl) {

    // Monta a lista com as contas de despesas
    case "MontaListaRelacionamentoDespesas":

          if ($rsContas->getNumLinhas() != 0) {

              $obLista = new Lista;
              $obLista->setMostraPaginacao( false );
              $obLista->setTitulo( "Dados Para o Relacionamento" );

              $obLista->setRecordSet( $rsContas );
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo("&nbsp;");
              $obLista->ultimoCabecalho->setWidth( 3 );
              $obLista->commitCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Despesa" );
              $obLista->ultimoCabecalho->setWidth( 72 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Despesa ExportacaoTCE" );
              $obLista->ultimoCabecalho->setWidth( 10 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Recebe Lancamento" );
              $obLista->ultimoCabecalho->setWidth( 15 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();

              $obLista->addDado();
              $obLista->ultimoDado->setCampo( "[cod_estrutural] - [descricao]" );
              $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
              $obLista->commitDado();

              /* TextBox Despesa ExportacaoTCE */
              $obTxtDespesaExportacaoTCE = new TextBox;
              $obTxtDespesaExportacaoTCE->setRotulo      ( "inEstruturalTce"    );
              $obTxtDespesaExportacaoTCE->setName        ( "inEstruturalTce_[exercicio]_[cod_conta]_"    );
              $obTxtDespesaExportacaoTCE->setSize        ( 8                    );
              $obTxtDespesaExportacaoTCE->setMaxLength   ( 8                    );
              $obTxtDespesaExportacaoTCE->setInteiro     ( true                 );
              $obTxtDespesaExportacaoTCE->setValue       ( "cod_estrutural_tce" );

              $obLista->addDadoComponente   ( $obTxtDespesaExportacaoTCE          );
              $obLista->ultimoDado->setCampo( "inEstruturalTce_"         );
              $obLista->commitDadoComponente();

              $obSelect = new Select;
              $obSelect->setRotulo    ( 'Lancamento'        );
              $obSelect->setName      ( 'boLancamento'      );
              $obSelect->setValue     ( "lancamento"        );
              $obSelect->setStyle     ( "width: 100px"      );
              $obSelect->addOption    ( "t", "Sim"          );
              $obSelect->addOption    ( "f","Não"           );
              $obSelect->setNull      ( false               );

              $obLista->addDadoComponente   ( $obSelect           );
              $obLista->ultimoDado->setCampo( "boLancamento"      );
              $obLista->commitDadoComponente();

              $obLista->montaHTML();
              $stHtml = $obLista->getHTML();
              $stHtml = str_replace("\n","",$stHtml);
              $stHtml = str_replace("  ","",$stHtml);
              $stHtml = str_replace("'","\\'",$stHtml);
          }
          // preenche a lista com innerHTML
          $stJs .= "\td.getElementById('spnRelacionamentoDespesas').innerHTML = '".$stHtml."';\r\n";

    break;
}
if($stJs)
    SistemaLegado::executaFrameOculto($stJs);
SistemaLegado::LiberaFrames();
?>
