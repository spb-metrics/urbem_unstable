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
    * Arquivo de instância para manutenção dos processos
    * Data de Criação: 11/10/2006

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    Casos de uso: uc-01.06.98

    $Id: FMManterProcesso.php 61555 2015-02-04 18:03:43Z diogo.zarpelon $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_PROT_MAPEAMENTO."TProtocoloProcesso.class.php";
include_once CAM_GA_PROT_COMPONENTES."ISelectClassificacaoAssunto.class.php";
include_once CAM_GA_PROT_COMPONENTES."IChkDocumentoProcesso.class.php";
include_once CAM_FRAMEWORK."legado/funcoesLegado.lib.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";

Sessao::write('codigo_processo',$_REQUEST['inCodigoProcesso']);

//Define o nome dos arquivos PHP
$stPrograma = "ManterProcesso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&pg=".Sessao::read('link_pg')."&pos=".Sessao::read('link_pos');;
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : 'alterar';

$stSQL = "
    SELECT  *
      FROM  SW_ANDAMENTO
     WHERE  (COD_ANDAMENTO = 1 OR COD_ANDAMENTO = 0)
       AND  COD_PROCESSO=".$_REQUEST['inCodigoProcesso']."
       AND  ANO_EXERCICIO = '".$_REQUEST['inAnoExercicio']."'
  ORDER BY  COD_ANDAMENTO DESC
     LIMIT  1";

$dbSQLSetor = new databaseLegado;
$dbSQLSetor->abreBd();
$dbSQLSetor->abreSelecao($stSQL);

if (!$dbSQLSetor->eof()) {
    $codOrgao          = $dbSQLSetor->pegaCampo("cod_orgao");
    $codAndamento      = $dbSQLSetor->pegaCampo("cod_andamento");
}

$obIMontaOrganograma = new IMontaOrganograma;
$obIMontaOrganograma->setNivelObrigatorio(1);
$obIMontaOrganograma->setCodOrgao($codOrgao);

$dbSQLSetor->limpaSelecao();
$dbSQLSetor->fechaBd();

$stSql = "SELECT cod_situacao FROM SW_PROCESSO WHERE COD_PROCESSO = ".$_GET['inCodigoProcesso']." AND ANO_EXERCICIO = '".$_GET['inAnoExercicio']."' ";
$stSQLSituacao = new databaseLegado;
$stSQLSituacao->abreBd();
$stSQLSituacao->abreSelecao($stSql);
if (!$stSQLSituacao->eof()) {
    $codSituacao       = $stSQLSituacao->pegaCampo("cod_situacao");
}
$stSQLSituacao->limpaSelecao();
$stSQLSituacao->fechaBd();

$obHdnCtrl = new hidden;
$obHdnCtrl->setName( 'stCtrl' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obTProtocoloProcesso = new TProtocoloProcesso();
$stProcesso = $obTProtocoloProcesso->mascararProcesso($_GET['inCodigoProcesso'], $_GET['inAnoExercicio']);

Sessao::write('filtro',array('inCodigoProcesso' => $_GET['inCodigoProcesso'], 'inAnoExercicio' => $_GET['inAnoExercicio']));

$obTProtocoloProcesso->setDado('cod_processo', $_GET['inCodigoProcesso']);
$obTProtocoloProcesso->setDado('ano_exercicio',$_GET['inAnoExercicio']);
$obTProtocoloProcesso->recuperaPorChave($rsProcesso);

$select = "SELECT
                AP.cod_atributo,
                AP.nom_atributo,
                AP.tipo,
                AP.valor_padrao
            FROM
                sw_atributo_protocolo AS AP,
                sw_assunto_atributo   AS AT
            WHERE
                AP.cod_atributo      = AT.cod_atributo AND
                AT.cod_classificacao = ".$_REQUEST['inCodigoClassificacao']." AND
                AT.cod_assunto       = ".$_REQUEST['inCodigoAssunto']."
            ORDER BY
                AP.nom_atributo";

$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$dbConfig->abreSelecao($select);

while (!($dbConfig->eof())) {
    $codAtributo = $dbConfig->pegaCampo("cod_atributo");
    $tipo        = $dbConfig->pegaCampo("tipo");
    $valorLista  = $dbConfig->pegaCampo("valor_padrao");

    if ($tipo == "l") {
        $lista = explode("\n", $valorLista);
    }
    $dbConfig->vaiProximo();
}

$stSQL = "  SELECT  AP.nom_atributo,
                    AP.cod_atributo,
                    AP.tipo,
                    AAV.valor
              FROM  sw_assunto_atributo_valor AS AAV,
                    sw_atributo_protocolo AS AP
             WHERE  AAV.cod_processo      = ".$_REQUEST['inCodigoProcesso']."      AND
                    AAV.exercicio         = '".$_REQUEST['inAnoExercicio']."'      AND
                    AAV.cod_assunto       = ".$_REQUEST['inCodigoAssunto']."       AND
                    AAV.cod_classificacao = ".$_REQUEST['inCodigoClassificacao']." AND
                    AAV.cod_atributo      = AP.cod_atributo
          ORDER BY  AP.nom_atributo";

$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$dbConfig->abreSelecao($stSQL);

if ($dbConfig->numeroDeLinhas > 0) {
    while (!($dbConfig->eof())) {
        $nomAtributo = $dbConfig->pegaCampo("nom_atributo");
        $tipo        = $dbConfig->pegaCampo("tipo");

        if ($tipo == "l") {
            $numValor = $dbConfig->pegaCampo("valor");
            $listaTipoCmb = explode("\n", $tipo);
        }

        if ($tipo == "t") {
            $stTexto = $dbConfig->pegaCampo("valor");
            $listaTipoTxt = explode("\n", $tipo);
        }

        if ($tipo == "n") {
            $numNumero = $dbConfig->pegaCampo("valor");
            $listaTipoNum = explode("\n", $tipo);
        }

        $dbConfig->vaiProximo();
    }
}

if ($lista == "") {
    $lista = array();
}
$rsLista = new RecordSet();
$rsLista->preenche($lista);

$obHdnProcesso = new hidden();
$obHdnProcesso->setName( 'stChaveProcesso' );
$obHdnProcesso->setValue( $_REQUEST['inCodigoProcesso'].'/'.$_REQUEST['inAnoExercicio'] );

$obLblProcesso = new Label();
$obLblProcesso->setRotulo("Processo");
$obLblProcesso->setValue($stProcesso);

$obTxtObservacoes = new TextArea();
$obTxtObservacoes->setRotulo('Observações');
$obTxtObservacoes->setNull(false);
$obTxtObservacoes->setName('stObservacoes');
$obTxtObservacoes->setCols(40);
$obTxtObservacoes->setRows(4);
$obTxtObservacoes->setValue($rsProcesso->getCampo('observacoes') );

$obTxtResumo = new TextBox();
$obTxtResumo->setName('stResumo');
$obTxtResumo->setRotulo('Assunto Resumido');
$obTxtResumo->setSize(80);
$obTxtResumo->setMaxLength(80);
$obTxtResumo->setValue( $rsProcesso->getCampo('resumo_assunto') );

$obISelectClassificacaoAssunto = new ISelectClassificacaoAssunto;
$obISelectClassificacaoAssunto->setNull                     ( false               );
$obISelectClassificacaoAssunto->obTxtChave->setName         ( 'codClassifAssunto' );
$obISelectClassificacaoAssunto->obCmbClassificacao->setName ( 'codClassificacao'  );
$obISelectClassificacaoAssunto->obCmbAssunto->setName       ( 'codAssunto'        );
$obISelectClassificacaoAssunto->obCmbClassificacao->setValue($rsProcesso->getCampo('cod_classificacao'));
$obISelectClassificacaoAssunto->obCmbAssunto->setValue($rsProcesso->getCampo('cod_assunto'));
$stCaminho = CAM_PROTOCOLO."protocolo/processos/".$pgOcul;
$stParametros = "'documento&codClassifAssunto=' + document.frm.codClassifAssunto.value";
$obISelectClassificacaoAssunto->obTxtChave->obEvento->setOnChange("ajaxJavaScript('".$stCaminho."',".$stParametros.");");
$obISelectClassificacaoAssunto->obCmbClassificacao->obEvento->setOnChange("document.getElementById('obCmpDocumento').style.display = 'none';");
$obISelectClassificacaoAssunto->obCmbAssunto->obEvento->setOnChange("document.getElementById('obCmpDocumento').style.display = 'none';");

$obIChkDocumentoProcesso = new IChkDocumentoProcesso();
$obIChkDocumentoProcesso->setCodigoClassificacao($_GET['inCodigoClassificacao']);
$obIChkDocumentoProcesso->setCodigoAssunto($_GET['inCodigoAssunto']);
$obIChkDocumentoProcesso->setCodProcesso($_REQUEST['inCodigoProcesso']);
$obIChkDocumentoProcesso->setAnoProcesso($_REQUEST['inAnoExercicio']);

$obFormulario = new Formulario();
$obIChkDocumentoProcesso->geraFormulario($obFormulario);
$obFormulario->montaInnerHTML();

$obSpnDocumentos = new Span();
$obSpnDocumentos->setId('obCmpDocumento');
$obSpnDocumentos->setValue( $obFormulario->getHTML() );
unset( $obFormulario );

$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Dados do processo');
$obFormulario->addHidden( $obHdnCtrl);
$obFormulario->addHidden($obHdnProcesso);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addComponente($obLblProcesso);
$obFormulario->addComponente($obTxtObservacoes);
$obFormulario->addComponente($obTxtResumo);
$obISelectClassificacaoAssunto->geraFormulario($obFormulario);

if ($codSituacao == '2' || ($codSituacao == '3' && $codAndamento == 0)) {
    $obIMontaOrganograma->geraFormulario($obFormulario);
    #$obMontaOrgUniDepSet->montaFormulario( $obFormulario );
}

$obFormulario->addSpan($obSpnDocumentos);

if ($dbConfig->numeroDeLinhas > 0) {
    $obFormulario->addTitulo('Atributos de Assunto de Processo');

    if ($listaTipoTxt[0] == "t") {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($stSQL);
        if ($dbConfig->numeroDeLinhas > 0) {
            while (!($dbConfig->eof())) {
                $codAtributo = $dbConfig->pegaCampo("cod_atributo");
                $nomAtributo = $dbConfig->pegaCampo("nom_atributo");
                $tipo        = $dbConfig->pegaCampo("tipo");

                if ($tipo == "t") {
                    $stTexto = $dbConfig->pegaCampo("valor");

                    $obTxtAtributosProcessos = new TextBox();
                    $obTxtAtributosProcessos->setName("valorAtributo[".$codAtributo."]");
                    $obTxtAtributosProcessos->setSize('60');
                    $obTxtAtributosProcessos->setMaxLength('50');
                    $obTxtAtributosProcessos->setRotulo($nomAtributo);
                    $obTxtAtributosProcessos->setValue($stTexto);

                    $obFormulario->addComponente($obTxtAtributosProcessos);
                }
                $dbConfig->vaiProximo();
            }
        }
    }
    if ($listaTipoNum[0] == "n") {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($stSQL);
        if ($dbConfig->numeroDeLinhas > 0) {
            while (!($dbConfig->eof())) {
                $codAtributo = $dbConfig->pegaCampo("cod_atributo");
                $nomAtributo = $dbConfig->pegaCampo("nom_atributo");
                $tipo        = $dbConfig->pegaCampo("tipo");

                if ($tipo == "n") {
                    $numNumero = $dbConfig->pegaCampo("valor");

                    $obTxtAtributosProcessosNum = new TextBox();
                    $obTxtAtributosProcessosNum->setName("valorAtributo[".$codAtributo."]");
                    $obTxtAtributosProcessosNum->setSize('60');
                    $obTxtAtributosProcessosNum->setMaxLength('50');
                    $obTxtAtributosProcessosNum->setRotulo($nomAtributo);
                    $obTxtAtributosProcessosNum->setValue($numNumero);

                    $obFormulario->addComponente($obTxtAtributosProcessosNum);
                }
                $dbConfig->vaiProximo();
            }
        }
    }
    if ($listaTipoCmb[0] == "l") {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($stSQL);
        if ($dbConfig->numeroDeLinhas > 0) {
            while (!($dbConfig->eof())) {
                $codAtributo = $dbConfig->pegaCampo("cod_atributo");
                $nomAtributo = $dbConfig->pegaCampo("nom_atributo");
                $tipo        = $dbConfig->pegaCampo("tipo");

                if ($tipo == "l") {
                    $numValor = $dbConfig->pegaCampo("valor");

                    $obCmbAtributosProcesso = new Select();
                    $obCmbAtributosProcesso->setName("valorAtributo[".$codAtributo."]");
                    $obCmbAtributosProcesso->setRotulo($nomAtributo);
                    $obCmbAtributosProcesso->setValue($numValor);
                    $obCmbAtributosProcesso->setStyle ( "width: 200px" );
                    $obCmbAtributosProcesso->addOption ( '', 'Selecione' );
                    while (list($key, $val) = each($lista)) {
                        $val = trim($val);
                        $obCmbAtributosProcesso->addOption($val, $val);
                    }

                    $obFormulario->addComponente($obCmbAtributosProcesso);
                }
                $dbConfig->vaiProximo();
            }
        }
    }
}

$obFormulario->Cancelar($pgList);
$obFormulario->show();
?>
