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
    * Página Oculta - Parâmetros do Arquivo CREDOR
    * Data de Criação   : 20/11/2004

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.08.06
*/

/*
$Log$
Revision 1.6  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EXP_NEGOCIO."RExportacaoTCERSArqCredor.class.php" );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new RExportacaoTCERSArqCredor();
$obRegra->obRExportacaoTCERSCredor->setExercicio      ( Sessao::getExercicio() );
if ($sessao->transf4['stAno']) {
    $obRegra->obRExportacaoTCERSCredor->setAno       ( $sessao->transf4['stAno']);
}
$obRegra->obRExportacaoTCERSCredor->setNumCGM         ( Sessao::read('numCgm') );

// Foram para dentro do case correspondente
// $obRegra->obRExportacaoTCERSCredor->listar            ( $rsCredor ) ;
// $obRegra->obRExportacaoTCERSCredor->listarConversao   ( $rsCredorConversao );

                /**
                * NOTA:     02-28022005
                * Adicionado por Lucas Stephanou
                * Data : 28/02/2005
                * Posto para fora do case por Anderson Konze
                * Data: 17/05/2006
                */
                $obRcTipo = new RecordSet;
                $obRegra->obRExportacaoTCERSCredor->listarTipos($obRcTipo);

                $obForm = new Form;
                $obForm->setAction('#');

                $obSelect = new Select;
                $obSelect->setName      ( 'mestre'      );
                $obSelect->setId        ( 'mestre'      );
                $obSelect->obEvento->setOnChange  ( "mudaSelects(this.value);");
                $obSelect->setRotulo    ( 'Tipo de Credor' );
                $obSelect->setStyle     ( "width: 400px"     );
                $obSelect->setNull      ( true               );
                $obSelect->setCampoId   ( "valor"            );
                $obSelect->setCampoDesc ( "desc"             );
                $obSelect->addOption    ( "", "Selecione"    );
                $obSelect->setValue     ( "tipo"             );
                $obSelect->preencheCombo( $obRcTipo          );

                $obFormulario = new Formulario;
                $obFormulario->addTitulo    ('Dados para preenchimento automático');
                $obFormulario->addForm      ($obForm)   ;
                $obFormulario->addComponente($obSelect) ;
                $obFormulario->montaInnerHTML();

                $stHtmlSelect = $obFormulario->getHTML();

                /**
                * FIM-NOTA: 02-28022005
                */

// Acoes por pagina
switch ($stCtrl) {

    // Monta a lista com os Credores e os combos para selecionar tipo
    case "MontaListaManterCredor":

          $obRegra->obRExportacaoTCERSCredor->listar ( $rsCredor ) ;
          if ($rsCredor->getNumLinhas() != 0) {

              $obLista = new Lista;
              $obLista->setMostraPaginacao( false );
              //$obLista->setTitulo( "Dados Para o Arquivo" );
              $obLista->setTitulo( "Dados de Credores do exercício atual" );

              $obLista->setRecordSet( $rsCredor );
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo("&nbsp;");
              $obLista->ultimoCabecalho->setWidth( 3 );
              $obLista->commitCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "CGM" );
              $obLista->ultimoCabecalho->setWidth( 50 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Tipo de Credor" );
              $obLista->ultimoCabecalho->setWidth( 15 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();

              $obLista->addDado();
              $obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
              $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
              $obLista->commitDado();

              /* Combo para Selecionar os Tipos */
              $obCmbTipo = new Select;
              $obCmbTipo->setName       ( 'inTipo_[numcgm]_'        );
              $obCmbTipo->setId         ( 'inTipo_[numcgm]_'        );
              $obCmbTipo->setRotulo     ( 'Tipo'                    );
              $obCmbTipo->setStyle      ( "width: 400px"            );
              $obCmbTipo->setNull       ( true                      );
              $obCmbTipo->setCampoId    ( "valor"                   );
              $obCmbTipo->setCampoDesc  ( "desc"                    );
              $obCmbTipo->addOption     ( "", "Selecione"           );
              $obCmbTipo->setValue      ( "tipo"                    );
              $obCmbTipo->preencheCombo ( $obRcTipo                 );

              $obLista->addDadoComponente   ( $obCmbTipo    );
              $obLista->ultimoDado->setCampo( "tipo"        );
              $obLista->commitDadoComponente(               );

              $obLista->montaHTML();
              $stHtml = $obLista->getHTML();
              $stHtml = str_replace("\n","",$stHtml);
              $stHtml = str_replace("  ","",$stHtml);
              $stHtml = str_replace("'","\\'",$stHtml);
          }
          // preenche a lista com innerHTML
          $stJs .= "\td.getElementById('spnManterCredor').innerHTML = '".$stHtml."';\r\n";
        break;

 case "MontaListaManterCredorConversao":
        $obRegra->obRExportacaoTCERSCredor->listarConversao ( $rsCredorConversao );
        if ($rsCredorConversao->getNumLinhas() != 0) {

              $obLista = new Lista;
              $obLista->setMostraPaginacao( false );
              $obLista->setTitulo( "Dados de Credores da Conversão de Dados" );

              $obLista->setRecordSet( $rsCredorConversao );
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo("&nbsp;");
              $obLista->ultimoCabecalho->setWidth( 3 );
              $obLista->commitCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Exercício" );
              $obLista->ultimoCabecalho->setWidth( 10 );
              $obLista->commitCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "CGM" );
              $obLista->ultimoCabecalho->setWidth( 40 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Tipo de Credor" );
              $obLista->ultimoCabecalho->setWidth( 15 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();

              $obLista->addDado();
              $obLista->ultimoDado->setCampo( "exercicio" );
              $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
              $obLista->commitDado();
              $obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
              $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
              $obLista->commitDado();

              /* Combo para Selecionar os Tipos */
              $obCmbTipo = new Select;
              $obCmbTipo->setName       ( 'inTipoConversao_[numcgm]_[exercicio]_'  );
              $obCmbTipo->setId         ( 'inTipoConversao_[numcgm]_[exercicio]_'  );
              $obCmbTipo->setRotulo     ( 'Tipo'                        );
              $obCmbTipo->setStyle      ( "width: 400px"                );
              $obCmbTipo->setNull       ( true                          );
              $obCmbTipo->setCampoId    ( "valor"                       );
              $obCmbTipo->setCampoDesc  ( "desc"                        );
              $obCmbTipo->addOption     ( "", "Selecione"               );
              $obCmbTipo->setValue      ( "tipo"                        );
              $obCmbTipo->preencheCombo ( $obRcTipo                     );

              $obLista->addDadoComponente   ( $obCmbTipo    );
              $obLista->ultimoDado->setCampo( "tipo"        );
              $obLista->commitDadoComponente(               );

              $obLista->montaHTML();
              $stHtml = $obLista->getHTML();
              $stHtml = str_replace("\n","",$stHtml);
              $stHtml = str_replace("  ","",$stHtml);
              $stHtml = str_replace("'","\\'",$stHtml);
          }
          // preenche a lista de Conversão com innerHTML
          $stJs .= "\td.getElementById('spnManterCredorConversao').innerHTML = '".$stHtml."';\r\n";

    break;
case "MontaListaManterCredorGeral":

          $obRegra->obRExportacaoTCERSCredor->listar ( $rsCredor ) ;
          if ($rsCredor->getNumLinhas() != 0) {

              $obLista = new Lista;
              $obLista->setMostraPaginacao( false );
              //$obLista->setTitulo( "Dados Para o Arquivo" );
              $obLista->setTitulo( "Dados de Credores do exercício atual" );

              $obLista->setRecordSet( $rsCredor );
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo("&nbsp;");
              $obLista->ultimoCabecalho->setWidth( 3 );
              $obLista->commitCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "CGM" );
              $obLista->ultimoCabecalho->setWidth( 50 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Tipo de Credor" );
              $obLista->ultimoCabecalho->setWidth( 15 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();

              $obLista->addDado();
              $obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
              $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
              $obLista->commitDado();

              /* Combo para Selecionar os Tipos */
              $obCmbTipo = new Select;
              $obCmbTipo->setName       ( 'inTipo_[numcgm]_'        );
              $obCmbTipo->setId         ( 'inTipo_[numcgm]_'        );
              $obCmbTipo->setRotulo     ( 'Tipo'                    );
              $obCmbTipo->setStyle      ( "width: 400px"            );
              $obCmbTipo->setNull       ( true                      );
              $obCmbTipo->setCampoId    ( "valor"                   );
              $obCmbTipo->setCampoDesc  ( "desc"                    );
              $obCmbTipo->addOption     ( "", "Selecione"           );
              $obCmbTipo->setValue      ( "tipo"                    );
              $obCmbTipo->preencheCombo ( $obRcTipo                 );

              $obLista->addDadoComponente   ( $obCmbTipo    );
              $obLista->ultimoDado->setCampo( "tipo"        );
              $obLista->commitDadoComponente(               );

              $obLista->montaHTML();
              $stHtml = $obLista->getHTML();
              $stHtml = str_replace("\n","",$stHtml);
              $stHtml = str_replace("  ","",$stHtml);
              $stHtml = str_replace("'","\\'",$stHtml);
          }
          // preenche a lista com innerHTML
          $stJs .= "\td.getElementById('spnManterCredor').innerHTML = '".$stHtml."';\r\n";

        $obRegra->obRExportacaoTCERSCredor->listarConversao ( $rsCredorConversao );
        if ($rsCredorConversao->getNumLinhas() != 0) {

              $obLista = new Lista;
              $obLista->setMostraPaginacao( false );
              $obLista->setTitulo( "Dados de Credores da Conversão de Dados" );

              $obLista->setRecordSet( $rsCredorConversao );
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo("&nbsp;");
              $obLista->ultimoCabecalho->setWidth( 3 );
              $obLista->commitCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Exercício" );
              $obLista->ultimoCabecalho->setWidth( 10 );
              $obLista->commitCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "CGM" );
              $obLista->ultimoCabecalho->setWidth( 40 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Tipo de Credor" );
              $obLista->ultimoCabecalho->setWidth( 15 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();

              $obLista->addDado();
              $obLista->ultimoDado->setCampo( "exercicio" );
              $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
              $obLista->commitDado();
              $obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
              $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
              $obLista->commitDado();

              /* Combo para Selecionar os Tipos */
              $obCmbTipo = new Select;
              $obCmbTipo->setName       ( 'inTipoConversao_[numcgm]_[exercicio]_'  );
              $obCmbTipo->setId         ( 'inTipoConversao_[numcgm]_[exercicio]_'  );
              $obCmbTipo->setRotulo     ( 'Tipo'                        );
              $obCmbTipo->setStyle      ( "width: 400px"                );
              $obCmbTipo->setNull       ( true                          );
              $obCmbTipo->setCampoId    ( "valor"                       );
              $obCmbTipo->setCampoDesc  ( "desc"                        );
              $obCmbTipo->addOption     ( "", "Selecione"               );
              $obCmbTipo->setValue      ( "tipo"                        );
              $obCmbTipo->preencheCombo ( $obRcTipo                     );

              $obLista->addDadoComponente   ( $obCmbTipo    );
              $obLista->ultimoDado->setCampo( "tipo"        );
              $obLista->commitDadoComponente(               );

              $obLista->montaHTML();
              $stHtml = $obLista->getHTML();
              $stHtml = str_replace("\n","",$stHtml);
              $stHtml = str_replace("  ","",$stHtml);
              $stHtml = str_replace("'","\\'",$stHtml);
          }
          // preenche a lista de Conversão com innerHTML
          $stJs .= "\td.getElementById('spnManterCredorConversao').innerHTML = '".$stHtml."';\r\n";

    break;
}

 // preenche a lista com innerHTML do form do select magico
 $stJs .= "d.getElementById('spnSelect').innerHTML = '".$stHtmlSelect."';\r\n";

if($stJs) SistemaLegado::executaFrameOculto($stJs);
    SistemaLegado::LiberaFrames();
?>
