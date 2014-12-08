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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCManterVeiculo.php 59920 2014-09-22 14:27:58Z arthur $

    * Casos de uso: uc-03.02.06
*/

setlocale(LC_ALL,'pt_BR');

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/empenho/classes/negocio/REmpenhoEmpenho.class.php';
include_once ( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioBem.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculoDocumento.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaTipoVeiculo.class.php' );
include_once ( CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeUsuario.class.php' );
include_once ( CAM_GP_PAT_COMPONENTES.'IPopUpBem.class.php' );
include_once ( CAM_GA_ADM_COMPONENTES.'IMontaLocalizacao.class.php' );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaInfracao.class.php' );
include_once( CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php' );
//Define o nome dos arquivos PHP
$stPrograma = "ManterVeiculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function montaListaDocumentos($arDocumentos)
{
    global $pgOcul;

    if ( !is_array($arDocumentos) ) {
        $arDocumentos = array();
    }

    $rsDocumentos = new RecordSet();
    $rsDocumentos->preenche( $arDocumentos );

    $obTable = new Table();
    $obTable->setRecordset( $rsDocumentos );
    $obTable->setSummary( 'Lista de Documentos do Veículo' );

    $obTable->Head->addCabecalho( 'Documento', 60 );
    $obTable->Head->addCabecalho( 'Vencimento', 10 );
    $obTable->Head->addCabecalho( 'Situação', 10 );

    $obTable->Body->addCampo( 'nom_documento', 'E' );
    $obTable->Body->addCampo( '[desc_mes]/[ano_documento]', 'C' );
    $obTable->Body->addCampo( 'desc_situacao', 'C' );

    $obTable->Body->addAcao( 'alterar', "JavaScript:ajaxJavaScript(  '".CAM_GP_FRO_INSTANCIAS."veiculo/".$pgOcul."?".Sessao::getId()."&stDocumento=%s&id=%s', 'montaAlteracaoDocumento' );", array( 'cod_documento','id' ) );
    $obTable->Body->addAcao( 'excluir', "JavaScript:ajaxJavaScript(  '".CAM_GP_FRO_INSTANCIAS."veiculo/".$pgOcul."?".Sessao::getId()."&stDocumento=%s&id=%s', 'excluirDocumento' );", array( 'cod_documento','id' ) );

    $obTable->montaHTML( true );

    return "$('spnDocumentos').innerHTML = '".$obTable->getHtml()."';";

}

function montaEmpenho()
{
    //instancia um formulario
    $obFormulario = new Formulario();

    //instancia um componente exercicio
    $stExercicio = new Exercicio;
    $stExercicio->setId('stExercicioEmpenho');
    $stExercicio->setName('stExercicioEmpenho');
    $stExercicio->setNull( true );
    $stExercicio->setObrigatorioBarra( true );

    // Define Objeto TextBox para Codigo da Entidade
    $obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
    $obEntidadeUsuario->obTextBox->setId('inCodEntidadeOculto');
    $obEntidadeUsuario->obTextBox->setName('inCodEntidadeOculto');
    $obEntidadeUsuario->setObrigatorioBarra( true );
    
    $obTxtEmpenho = new Inteiro();
    $obTxtEmpenho->setRotulo ( "*Empenho Original" );
    $obTxtEmpenho->setTitle  ( "Informe o número do empenho original");
    $obTxtEmpenho->setName   ( "inCodigoEmpenho" );
    $obTxtEmpenho->setId     ( "inCodigoEmpenho" );
    $obTxtEmpenho->setNull   ( true );
    $obTxtEmpenho->setObrigatorioBarra( true );

    $obFormulario->addComponente( $stExercicio );
    $obFormulario->addComponente( $obEntidadeUsuario );
    $obFormulario->addComponente( $obTxtEmpenho );

    $obFormulario->montaInnerHTML();
    
    return $obFormulario->getHTML();

}

switch ($stCtrl) {
    case 'preencheDetalheBem' :
        if ($_REQUEST['inCodBem'] != '') {
            //recupera a localizacao do bem
            $obTPatrimonioBem = new TPatrimonioBem();
            $obTPatrimonioBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
            $obTPatrimonioBem->recuperaRelacionamentoAnalitico( $rsBem );
            
            //label para o exercicio
            $obLblExercicio = new Label();
            $obLblExercicio->setRotulo( 'Exercício do Empenho' );
            $obLblExercicio->setValue( $rsBem->getCampo('exercicio') );

            //label para a entidade
            $obLblEntidade = new Label();
            $obLblEntidade->setRotulo( 'Entidade' );
            $obLblEntidade->setValue( $rsBem->getCampo('cod_entidade').' - '.$rsBem->getCampo('nom_entidade') );
            
            //label para a orgao
            $obLbl_Orgao = new Label();
            $obLbl_Orgao->setRotulo( 'Órgão' );
            $obLbl_Orgao->setValue( $rsBem->getCampo('orgao_num_orgao').' - '.$rsBem->getCampo('orgao_nom_orgao') );
            
            //label para a unidade
            $obLbl_Unidade = new Label();
            $obLbl_Unidade->setRotulo( 'Unidade' );
            $obLbl_Unidade->setValue( $rsBem->getCampo('unidade_num_unidade').' - '.$rsBem->getCampo('unidade_nom_unidade') );

            //label para  o empenho
            $obLblEmpenho = new Label();
            $obLblEmpenho->setRotulo( 'Número do Empenho' );
            $obLblEmpenho->setValue( $rsBem->getCampo('cod_empenho') );

            //label para  o nota fiscal
            $obLblNotaFiscal = new Label();
            $obLblNotaFiscal->setRotulo( 'Nota Fiscal' );
            $obLblNotaFiscal->setValue( $rsBem->getCampo('nota_fiscal') );

//----- Início do bloco que busca órgãos através da classificação
            if ( $rsBem->getNumLinhas() == 1 ) {
                $obTPatrimonioBem = new TPatrimonioBem();
                $obTPatrimonioBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                $obTPatrimonioBem->recuperaOrgaoBem($rsPatrimonioBem);
                $inCodOrgao = $rsPatrimonioBem->getCampo('cod_orgao');
                
                $obTPatrimonioBem = new TPatrimonioBem();
                $obTPatrimonioBem->setDado( 'cod_orgao', $inCodOrgao );
                $obTPatrimonioBem->recuperaOrganogramaBem($rsPatrimonioBem);
                $inCodOrganograma = $rsPatrimonioBem->getCampo('cod_organograma');
                
                $obTPatrimonioBem = new TPatrimonioBem();
                $obTPatrimonioBem->setDado( 'cod_orgao', $inCodOrgao );
                $obTPatrimonioBem->recuperaCountOrgaoNivelBem($rsPatrimonioBem);
                $inCount = $rsPatrimonioBem->getCampo('count');
                
                $obTPatrimonioBem = new TPatrimonioBem();
                $obTPatrimonioBem->setDado( 'cod_orgao', $inCodOrgao );
                $obTPatrimonioBem->setDado( 'cod_organograma', $inCodOrganograma );
                $obTPatrimonioBem->recuperaVwOrgaoNivelBem($rsPatrimonioBem, ' WHERE cod_orgao = '.$inCodOrgao.' AND cod_organograma = '.$inCodOrganograma);
                $classificacao = explode('.', $rsPatrimonioBem->getCampo('orgao'));
                
                $orgao = '';
                for($j=0; $j<count($classificacao); $j++) {
                    $orgao.= $classificacao[$j].'.';
                    $arOrgao[] = substr($orgao, 0, (strlen($orgao)-1));
                }

                if ($orgao != '') {
                    $orgao = substr($orgao, 0, (strlen($orgao)-1));
                }
                
                for($i=0; $i<count($classificacao); $i++) {
                    $obTPatrimonioBem = new TPatrimonioBem();
                    $obTPatrimonioBem->recuperaVwOrgaoNivelBem($rsPatrimonioBem, " WHERE orgao_reduzido = '".$arOrgao[$i]."'");
                    $inCodOrgao = $rsPatrimonioBem->getCampo('cod_orgao');
                    
                    if ($inCodOrgao != '') {
                        $obTPatrimonioBem = new TPatrimonioBem();
                        $obTPatrimonioBem->setDado( 'cod_orgao', $inCodOrgao );
                        $obTPatrimonioBem->recuperaOrgaoDescricaoBem($rsPatrimonioBem);
                        
                        $arOrgaoDescricao[] = $rsPatrimonioBem->getCampo('descricao');
                    }
                }
//----- Término do bloco que busca órgãos através da classificação
                
                //pega a mascara para o localizacao
                $arMascara = explode('.',sistemaLegado::pegaConfiguracao( 'mascara_local',2 ));
                $arMascara[4] = explode('/',$arMascara[4]);

                //label para a localizacao
                $obLblLocalizacao = new Label();
                $obLblLocalizacao->setRotulo( 'Localização Atual' );
                $obLblLocalizacao->setValue( $orgao );

                //label para o orgao
                $obLblOrgao = new Label();
                $obLblOrgao->setRotulo( 'Órgão' );
                $obLblOrgao->setValue( $arOrgaoDescricao[0] );

                //label para a unidade
                $obLblUnidade = new Label();
                $obLblUnidade->setRotulo( 'Unidade' );
                $obLblUnidade->setValue( $arOrgaoDescricao[1] );

                //label para o departamento
                $obLblDepartamento = new Label();
                $obLblDepartamento->setRotulo( 'Departamento' );
                $obLblDepartamento->setValue( $arOrgaoDescricao[2] );

                //label para a setor
                $obLblSetor = new Label();
                $obLblSetor->setRotulo( 'Setor' );
                $obLblSetor->setValue( $rsBem->getCampo('nom_setor') );

                //label para a unidade
                $obLblLocal = new Label();
                $obLblLocal->setRotulo( 'Local' );
                $obLblLocal->setValue( $rsBem->getCampo('nom_local') );

                //cria um form para os labels da localizacao
                $obFormulario = new Formulario();
                $obFormulario->addComponente( $obLblExercicio );
                $obFormulario->addComponente( $obLblEntidade );
                
                $obFormulario->addComponente( $obLbl_Orgao );
                $obFormulario->addComponente( $obLbl_Unidade );
                
                $obFormulario->addComponente( $obLblEmpenho );
                $obFormulario->addComponente( $obLblNotaFiscal );

                $obFormulario->addComponente( $obLblLocalizacao );
                $obFormulario->addComponente( $obLblOrgao );
                $obFormulario->addComponente( $obLblUnidade );
                $obFormulario->addComponente( $obLblDepartamento );
                $obFormulario->addComponente( $obLblSetor );
                $obFormulario->addComponente( $obLblLocal );

                $obFormulario->montaInnerHTML();

                $stJs = "$('spnDados').innerHTML = '".$obFormulario->getHTML()."';";

                $obForm = new Form();

                //cria um formulario para os dados do responsavel pelo bem
                $obFormulario = new Formulario();
                $obFormulario->addTitulo( 'Dados do Responsável' );

                if ( $rsBem->getCampo('num_responsavel') != '' ) {
                    //cria um hidden para o cod_responsavel
                    $obHdnCodResponsavel = new Hidden();
                    $obHdnCodResponsavel->setName('inCodResponsavel');
                    $obHdnCodResponsavel->setValue( $rsBem->getCampo('num_responsavel') );

                    //cria um label para o responsavel
                    $obLblResponsavel = new Label();
                    $obLblResponsavel->setRotulo( 'Responsável');
                    $obLblResponsavel->setValue( $rsBem->getCampo('num_responsavel').' - '.$rsBem->getCampo('nom_responsavel') );

                    //cria um label para a data de inicio do responsavel
                    $obDtInicio = new Data();
                    $obDtInicio->setRotulo( 'Data de Início' );
                    //$obDtInicio->setTitle( 'Informe a data de início do responsável.' );
                    $obDtInicio->setValue( $rsBem->getCampo('dt_inicio') );
                    $obDtInicio->setName( 'dtInicio' );
                    $obDtInicio->setLabel( true );

                    $obFormulario->addHidden( $obHdnCodResponsavel );
                    $obFormulario->addComponente( $obLblResponsavel );
                    $obFormulario->addComponente( $obDtInicio );
                } else {

                    //instancia o componente IPopUpCGMVinculado para o responsavel
                    $obIPopUpResponsavel = new IPopUpCGMVinculado( $obForm );
                    $obIPopUpResponsavel->setTabelaVinculo    ( 'sw_cgm_pessoa_fisica' );
                    $obIPopUpResponsavel->setCampoVinculo     ( 'numcgm'               );
                    $obIPopUpResponsavel->setNomeVinculo      ( 'Responsável'          );
                    $obIPopUpResponsavel->setRotulo           ( 'Responsável'          );
                    $obIPopUpResponsavel->setTitle            ( 'Informe o reponsável pelo veículo.' );
                    $obIPopUpResponsavel->setName             ( 'stNomResponsavel'       );
                    $obIPopUpResponsavel->setId               ( 'stNomResponsavel'       );
                    //$obIPopUpResponsavel->setValue            ( $rsVeiculo->getCampo('nom_responsavel') );
                    $obIPopUpResponsavel->obCampoCod->setName ( 'inCodResponsavel'       );
                    $obIPopUpResponsavel->obCampoCod->setId   ( 'inCodResponsavel'       );
                    //$obIPopUpResponsavel->obCampoCod->setValue( $rsVeiculo->getCampo('cod_responsavel') );
                    $obIPopUpResponsavel->setNull             ( false                    );

                    //instancia um componente data para o inicio do responsavel
                    $obDtInicio = new Data();
                    $obDtInicio->setRotulo('Data de Início');
                    $obDtInicio->setTitle('Informe a data de início do responsável.');
                    $obDtInicio->setName('dtInicio');
                    $obDtInicio->setId('dtInicio');
                    $obDtInicio->setNull(false);

                    $obFormulario->addComponente( $obIPopUpResponsavel );
                    $obFormulario->addComponente( $obDtInicio );

                }

                $obFormulario->montaInnerHTML();

                $stJs .= "$('spnResponsavel').innerHTML = '".$obFormulario->getHTML()."';";
            } else {
                $stJs .= "$('spnDados').innerHTML = '';";
            }
        } else {
            $stJs .= "$('spnDados').innerHTML = '';";
        }
        break;

    case 'montaEmpenho' :

        if ($_REQUEST['stSituacao'] == 'pago') {
            $stJs .= "$('spnEmpenho').innerHTML = '".montaEmpenho()."';";
        } else {
            $stJs .= "$('spnEmpenho').innerHTML = '';";
        }
        break;

    case 'montaOrigem' :
        if ($_REQUEST['stOrigem'] != '') {
            //cria um form
            $obForm = new Form();
            $obForm->setAction ($pgProc);
            $obForm->setTarget ("oculto");

            $obFormulario = new Formulario();

            //se for proprio, monta o formulario com ipoup
            if ($_REQUEST['stOrigem'] == 'proprio') {
                //seta um componente ipopup
                $obIPopUpBem = new IPopUpBem( $obForm );
                $obIPopUpBem->setRotulo     ( 'Código do Bem' );
                $obIPopUpBem->setValue      ( $_REQUEST['stNomPropriedade'] );
                $obIPopUpBem->obCampoCod->setValue( $_REQUEST['inCodPropriedade'] );
                $obIPopUpBem->obCampoCod->obEvento->setOnBlur( "ajaxJavaScript('".$pgOcul.'?'.Sessao::getId()."&inCodBem='+this.value,'preencheDetalheBem');" );

                //span para os dados do bem
                $obSpnDados = new Span();
                $obSpnDados->setId( 'spnDados' );

                //instancia um formulario
                $obFormulario->addComponente( $obIPopUpBem );
                $obFormulario->addSpan      ( $obSpnDados );
            } elseif ($_REQUEST['stOrigem'] == 'terceiro') {

                $obSlEntidade = new Select();
                $obSlEntidade->setRotulo( 'Entidade' );
                $obSlEntidade->setTitle( 'Selecione a entidade do bem.' );
                $obSlEntidade->setName( 'inCodEntidade' );
                $obSlEntidade->setId( 'inCodEntidade' );
                $obSlEntidade->setValue($_REQUEST['inCodEntidade'] );
                $obSlEntidade->addOption( '','Selecione' );
                $obSlEntidade->setNull( true );

                $obExercicio = new Exercicio();
                $obExercicio->setRotulo( 'Exercício' );
                $obExercicio->setNull( true);
                $obExercicio->setName( stExercicioEntidade);
                $obExercicio->setId( 'stExercicioEntidade' );
                $obExercicio->setValue( $_REQUEST['stExercicioEntidade'] );
                $obExercicio->obEvento->setOnChange( "montaParametrosGET( 'preencheComboEntidade', 'stExercicioEntidade,hdnInCodEntidade'); " );
                
                $obTxtOrgao = new TextBox;
                $obTxtOrgao->setRotulo   ("Órgão");
                $obTxtOrgao->setTitle    ("Informe o órgão para filtro");
                $obTxtOrgao->setName     ("inCodOrgaoTxt");
                $obTxtOrgao->setId       ("inCodOrgaoTxt");
                $obTxtOrgao->setValue    ($_REQUEST['inCodOrgao']);
                $obTxtOrgao->setSize     (6);
                $obTxtOrgao->setMaxLength(3);
                $obTxtOrgao->setInteiro  (true);
                if ($_REQUEST['inCodOrgao'] != '' && $_REQUEST['inCodUnidade'] != '') {
                    $obTxtOrgao->obEvento->setOnSubmit("buscaValor('MontaUnidade','inCodUnidade');");
                }else {
                    $obTxtOrgao->obEvento->setOnChange("montaParametrosGET('MontaUnidade');" );
                }
                
                $obTxtUnidade = new TextBox;
                $obTxtUnidade->setRotulo   ("Unidade");
                $obTxtUnidade->setTitle    ("Informe a unidade para filtro");
                $obTxtUnidade->setName     ("inCodUnidadeTxt");
                $obTxtUnidade->setId       ("inCodUnidadeTxt");
                $obTxtUnidade->setValue    ($_REQUEST['inCodUnidade']);
                $obTxtUnidade->setSize     (6);
                $obTxtUnidade->setMaxLength(3);
                $obTxtUnidade->setInteiro  (true);
                  

                include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRPAnuLiqEstLiq.class.php";
                $obREmpenhoRPAnuLiqEstLiq = new REmpenhoRelatorioRPAnuLiqEstLiq;
                $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio() );
                $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar($rsCombo);
             
                $obCmbOrgao = new Select;
                $obCmbOrgao->setRotulo            ("Órgão");
                $obCmbOrgao->setName              ("inCodOrgao");
                $obCmbOrgao->setId                ("inCodOrgao");
                $obCmbOrgao->setValue             ($_REQUEST['inCodOrgao']);
                $obCmbOrgao->setStyle             ("width: 400px");
                $obCmbOrgao->setCampoID           ("num_orgao");
                $obCmbOrgao->setCampoDesc         ("nom_orgao");
                $obCmbOrgao->addOption            ('', 'Selecione');
                $obCmbOrgao->preencheCombo        ($rsCombo);
                $obCmbOrgao->obEvento->setOnChange("montaParametrosGET('MontaUnidade');" );

                $obCmbUnidade= new Select;
                $obCmbUnidade->setRotulo   ("Unidade");
                $obCmbUnidade->setName     ("inCodUnidade");
                $obCmbUnidade->setId       ("inCodUnidade");
                $obCmbUnidade->setValue    ($_REQUEST['inCodUnidade']);
                $obCmbUnidade->setStyle    ("width: 400px");
                $obCmbUnidade->setCampoID  ("cod_unidade");
                $obCmbUnidade->setCampoDesc("descricao");                
                $obCmbUnidade->addOption   ('', 'Selecione');
                
                //instancia o componente IPopUpCGM para o proprietario
                $obIPopUpProprietario = new IPopUpCGM      ( $obForm );
                $obIPopUpProprietario->setRotulo           ( 'Proprietário'          );
                $obIPopUpProprietario->setTitle            ( 'Informe o proprietário do veículo.' );
                $obIPopUpProprietario->setName             ( 'stNomProprietario'      );
                $obIPopUpProprietario->setId               ( 'stNomProprietario'      );
                $obIPopUpProprietario->obCampoCod->setName ( 'inCodProprietario'      );
                $obIPopUpProprietario->obCampoCod->setId   ( 'inCodProprietario'      );
                $obIPopUpProprietario->obCampoCod->setValue( $_REQUEST['inCodPropriedade'] );
                $obIPopUpProprietario->setValue( $_REQUEST['stNomPropriedade'] );
                $obIPopUpProprietario->setNull             ( false                    );

                $arOrganogramaLocal = explode('.',$_REQUEST['stLocalizacao']);

                include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
                include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php";

                $obIMontaOrganograma = new IMontaOrganograma(false);
                $obIMontaOrganograma->setCodOrgao($arOrganogramaLocal[0]);
                $obIMontaOrganograma->setCadastroOrganograma(true);
                $obIMontaOrganograma->setNivelObrigatorio(1);

                $obIMontaOrganograma->setHiddenEvalName('hdnOrigem');

                $obIMontaOrganogramaLocal = new IMontaOrganogramaLocal;
                $obIMontaOrganogramaLocal->setValue($arOrganogramaLocal[1]);
                $obIMontaOrganogramaLocal->setNull(false);
                $obFormulario->addComponente( $obExercicio );
                $obFormulario->addComponente( $obSlEntidade );
                $obFormulario->addComponenteComposto($obTxtOrgao, $obCmbOrgao);
                $obFormulario->addComponenteComposto($obTxtUnidade, $obCmbUnidade);
                $obFormulario->addComponente( $obIPopUpProprietario );
                $obIMontaOrganograma->geraFormulario($obFormulario);
                $obIMontaOrganogramaLocal->geraFormulario($obFormulario);

                if ($obIMontaOrganograma->getScript()) {
                    $stEval = $stEval.$obIMontaOrganograma->getScript();
                }
            }

            $obFormulario->montaInnerHTML();

            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);
            
            $stJs .= "$('spnOrigem').innerHTML = '".$obFormulario->getHTML()."'; ";
            $stJs .= "montaParametrosGET( 'preencheComboEntidade');";
            $stJs .= "jq('#inCodEntidade').val(".$_REQUEST['inCodEntidade'].");"; 
            $stJs .= "montaParametrosGET( 'MontaUnidade');";
            $stJs .= "$('hdnOrigem').value = '".$stEval."'; ";
            
        } else {
            $stJs .= "$('spnOrigem').innerHTML = '';";
            $stJs .= "$('hdnOrigem').value = ''; ";
        }
        break;
   case "MontaUnidade":
        $stJs .= "if(f.inCodUnidade){ limpaSelect(f.inCodUnidade,0); } \n";
        $stJs .= "jq('#inCodUnidadeTxt').value = ''; \n";
        $stJs .= "jq('#inCodUnidade').append( new Option('Selecione','', 'selected')) ;\n";

        if ($_REQUEST["inCodOrgao"]) {
            include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRPAnuLiqEstLiq.class.php";
            $obREmpenhoRPAnuLiqEstLiq = new REmpenhoRelatorioRPAnuLiqEstLiq;
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inCodOrgao"]);
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->setExercicio(Sessao::getExercicio() );
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, $stFiltro,"", $boTransacao );

            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo("num_unidade");
                $stDesc = $rsCombo->getCampo("nom_unidade");
                $stJs .= "jQuery('#inCodUnidade').append( new Option('".$rsCombo->getCampo("nom_unidade")."','".$rsCombo->getCampo("num_unidade")."' )); \n";
                $rsCombo->proximo();
            }
        } 
        if($_REQUEST["hdnInCodUnidade"] !=""){
            $stJs.= "jQuery('#inCodUnidade').val(".$_REQUEST["hdnInCodUnidade"]."); ";
        }
        
    break;
    case 'preencheComboEntidade' :

        $stJs.= "if(d.getElementById('inCodEntidade')) { limpaSelect($('inCodEntidade'),1); } "; 

        if ($_REQUEST['stExercicioEntidade'] ){
            //cria o filtro
            $stFiltro = " AND E.exercicio = '".$_REQUEST['stExercicioEntidade']."' ";
            //recupera todos as entidades para o exercicio
            
            $obTOrcamentoEntidade = new TOrcamentoEntidade();
            $obTOrcamentoEntidade->recuperaRelacionamento( $rsEntidades, $stFiltro, ' ORDER BY cod_entidade ' );
            if ( $rsEntidades->getNumLinhas() > 0 ) {
                $inCount = 1;
                while ( !$rsEntidades->eof() ) {
                    if(( $_REQUEST['inCodEntidade'] == $rsEntidades->getCampo('cod_entidade') )){
                        $stSelected = 'true';
                    }else{
                        $stSelected = 'false';
                    }
                    $stJs .= "jq('#".inCodEntidade."').addOption('".$rsEntidades->getCampo('cod_entidade')."','".$rsEntidades->getCampo('cod_entidade')." - ".$rsEntidades->getCampo('nom_cgm')."',".$stSelected.");";
                    $rsEntidades->proximo();
                    $inCount++;
                }
                if($_REQUEST["hdnInCodEntidade"]&&$_REQUEST["hdnInCodEntidade"]!='')
                    $stJs.= "$('inCodEntidade').value = ".$_REQUEST["hdnInCodEntidade"]."; ";
            } else {
                $stJs.= "$('stExercicioEntidade').value = ''; ";
                $stJs.= "alertaAviso('Exercício sem entidades cadastradas.','form','erro','".Sessao::getId()."');";
            }
        }
    break;
    case 'buscaEmpenho':
        if ($_REQUEST["inCodigoEmpenho"] != '' && $_REQUEST["inCodEntidade"] != '' && $_REQUEST['stExercicioEmpenho'] != '') {

            $obREmpenhoEmpenho = new REmpenhoEmpenho;

            $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade ( $_REQUEST["inCodEntidade"]  );
            $obREmpenhoEmpenho->setExercicio  ( $_REQUEST['stExercicioEmpenho'] );
            $obREmpenhoEmpenho->setCodEmpenhoInicial ( $_REQUEST["inCodigoEmpenho"] );
            $obREmpenhoEmpenho->setCodEmpenhoFinal ( $_REQUEST["inCodigoEmpenho"] );
            $obREmpenhoEmpenho->setSituacao ( 5 );

            $obREmpenhoEmpenho->listar($rsLista);

            if ($rsLista->getNumLinhas() > 0 ) {
                $obREmpenhoEmpenho->setCodEmpenho ( $_REQUEST["inCodigoEmpenho"] );
                $obREmpenhoEmpenho->consultar();
                $stNomFornecedor = ( $rsLista->getCampo( 'nom_fornecedor' ) ) ? str_replace( "'","\'",$rsLista->getCampo( "nom_fornecedor" )):'&nbsp;';
                $stJs .= "d.getElementById('stNomFornecedor').innerHTML='".$stNomFornecedor."';";
            } else {
                $stJs .= "$('inCodigoEmpenho').value='';";
                $stJs .= "$('stNomFornecedor').innerHTML = '&nbsp;';";
                $stJs .= "alertaAviso('Empenho informado está anulado ou não existe.','frm','erro','".Sessao::getId()."'); \n";
            }
        } else {
                $stJs .= "$('inCodigoEmpenho').value='';";
                $stJs .= "$('stNomFornecedor').innerHTML = '&nbsp;';";
        }
        break;
    case 'incluirDocumento' :
        $stJs = isset($stJs) ? $stJs : null;
    
    if ($_REQUEST['stDocumento'] == '') {
            $stMensagem = 'Selecione um documento.';
        } elseif ($_REQUEST['stExercicio'] == '') {
            $stMensagem = 'Selecione o ano de vencimento.';
        } elseif ( $_REQUEST['stExercicio'] != '' AND $_REQUEST['stExercicio'] > Sessao::getExercicio() ) {
            $stMensagem = 'O ano do vencimento deve ser menor ou igual ao ano atual.';
        } elseif ($_REQUEST['inMes'] == '') {
            $stMensagem = 'Selecione o mês de vencimento.';
        } elseif ($_REQUEST['stSituacao'] == '') {
            $stMensagem = 'Selecione a situação do documento.';
        } elseif ($_REQUEST['stSituacao'] == 'pago') {
            if ($_REQUEST['stExercicioEmpenho'] == '') {
                $stMensagem = 'Informe o exercícío do empenho.';
            } elseif ($_REQUEST['inCodEntidadeOculto'] == '') {
                $stMensagem = 'Informe a entidade do empenho.';
            }
            if ($_REQUEST['inCodigoEmpenho'] == '') {
                $stMensagem = 'Informe o código do empenho.';
            }
        }
        if ( count( Sessao::read('arDocumentos') ) > 0 ) {
           foreach ( Sessao::read('arDocumentos') AS $arTemp ) {
                if ( ($arTemp['cod_documento'] == $_REQUEST['stDocumento']) AND ($arTemp['ano_documento'] == $_REQUEST['stExercicio']) ) {
                    $stMensagem = 'Este documento já está na lista.';
                    break;
                }
            }
        }
        if (!$stMensagem) {

            $arDocumentos = Sessao::read('arDocumentos');
            $inCount = count($arDocumentos);
            $arDocumentos[$inCount]['id']            = $inCount + 1;
            $arDocumentos[$inCount]['cod_documento'] = $_REQUEST['stDocumento'];
            $arDocumentos[$inCount]['nom_documento'] = sistemaLegado::pegaDado( 'nom_documento', 'frota.documento', ' WHERE cod_documento = '.$_REQUEST['stDocumento'].' ' );
            $arDocumentos[$inCount]['mes']           = $_REQUEST['inMes'];
            $arDocumentos[$inCount]['ano_documento'] = $_REQUEST['stExercicio'];
            $arDocumentos[$inCount]['desc_mes']      = strftime('%b',mktime(0,0,0,$_REQUEST['inMes'],1,$_REQUEST['stExercicio']));
            $arDocumentos[$inCount]['situacao']      = ($_REQUEST['stSituacao'] == 'pago') ? true : false;
            $arDocumentos[$inCount]['desc_situacao'] = ($_REQUEST['stSituacao'] == 'pago') ? 'Pago' : 'Não Pago';
            if ($_REQUEST['stSituacao'] == 'pago') {
                $arDocumentos[$inCount]['exercicio_empenho']    = $_REQUEST['stExercicioEmpenho'];
                $arDocumentos[$inCount]['cod_entidade'] = $_REQUEST['inCodEntidadeOculto'];
                $arDocumentos[$inCount]['cod_empenho']  = $_REQUEST['inCodigoEmpenho'];
            }
            $stJs .= montaListaDocumentos( $arDocumentos );
            $stJs .= "$('stDocumento').selectedIndex = 0;";
            $stJs .= "$('stExercicio').value = '".Sessao::getExercicio()."';";
            $stJs .= "$('inMes').selectedIndex = 0;";
            $stJs .= "$('stSituacao1').checked = false;";
            $stJs .= "$('stSituacao2').checked = false;";
            $stJs .= "$('spnEmpenho').innerHTML = ''; ";

            Sessao::write('arDocumentos' , $arDocumentos);
        } else {
            $stJs .= "alertaAviso('".$stMensagem."','frm','erro','".Sessao::getId()."'); \n";
        }
        break;

    case 'montaAlteracaoDocumento' :

        $arDocumentos = Sessao::read('arDocumentos');
        $inCount = $_REQUEST['id'];
        $inCount = $inCount - 1;

        $stJs .= "$('hdnId').value = '".$_REQUEST['id']."';";
        $stJs .= "$('stDocumento').selectedIndex = '".$arDocumentos[$inCount]['cod_documento']."';";
        $stJs .= "$('stExercicio').value = '".$arDocumentos[$inCount]['ano_documento']."';";
        $stJs .= "$('inMes').selectedIndex = '".$arDocumentos[$inCount]['mes']."';";
        if ($arDocumentos[$inCount]['situacao']) {
            $stJs .= "$('stSituacao1').checked = true;";
            $stJs .= "$('spnEmpenho').innerHTML = '".montaEmpenho()."';";
            $stJs .= "$('stExercicioEmpenho').value = '".$arDocumentos[$inCount]['exercicio_empenho']."';";
            $stJs .= "$('inCodEntidadeOculto').value = '".$arDocumentos[$inCount]['cod_entidade']."';";
            $stJs .= "$('stNomEntidade').selectedIndex = '".$arDocumentos[$inCount]['cod_entidade']."';";
            $stJs .= "$('inCodigoEmpenho').value = '".$arDocumentos[$inCount]['cod_empenho']."';";
            //$stJs .= "$('stNomFornecedor').value = '".$arDocumentos[$inCount]['nom_empenho']."';";
            //$stJs .= "$('stNomFornecedor').innerHTML = '".$arDocumentos[$inCount]['nom_empenho']."';";
        } else {
            $stJs .= "$('stSituacao2').checked = true;";
        }
        $stJs .= "$('incluiDocumento').value = 'Alterar';";
        $stJs .= "$('incluiDocumento').setAttribute( 'onclick','montaParametrosGET(\'alterarDocumento\',\'stDocumento,stExercicio,inMes,stSituacao,stExercicioEmpenho,inCodEntidadeOculto,inCodigoEmpenho,stNomFornecedor,hdnId\');');";
        break;

    case 'alterarDocumento' :
        if ($_REQUEST['stDocumento'] == '') {
            $stMensagem = 'Selecione um documento.';
        } elseif ($_REQUEST['stExercicio'] == '') {
            $stMensagem = 'Selecione o ano de vencimento.';
        } elseif ($_REQUEST['inMes'] == '') {
            $stMensagem = 'Selecione o mes de vencimento.';
        } elseif ($_REQUEST['stSituacao'] == '') {
            $stMensagem = 'Selecione a situação do documento.';
        } elseif ($_REQUEST['stSituacao'] == 'pago') {
            if ($_REQUEST['stExercicio'] == '') {
                $stMensagem = 'Informe o exercícío do empenho.';
            } elseif ($_REQUEST['inCodEntidadeOculto'] == '') {
                $stMensagem = 'Informe a entidade do empenho.';
            }
            if ($_REQUEST['inCodigoEmpenho'] == '') {
                $stMensagem = 'Informe o código do empenho.';
            }
        }
        
        if ( count( Sessao::read('arDocumentos') ) > 0 ) {
            foreach ( Sessao::read('arDocumentos') AS $arTemp ) {
                if ($arTemp['cod_documento'] == $_REQUEST['stDocumento'] AND $arTemp['ano_documento'] == $_REQUEST['stExercicio'] AND $arTemp['id'] != $_REQUEST['hdnId']) {
                    $stMensagem = 'Este documento já está na lista.';
                    break;
                }
            }
        }
        if (!$stMensagem) {

            $arDocumentos = Sessao::read('arDocumentos');

            $inCount = $_REQUEST['hdnId'];
            $inCount = $inCount - 1;
            $arDocumentos[$inCount]['id']            = $_REQUEST['hdnId'];
            $arDocumentos[$inCount]['cod_documento'] = $_REQUEST['stDocumento'];
            $arDocumentos[$inCount]['nom_documento'] = sistemaLegado::pegaDado( 'nom_documento', 'frota.documento', ' WHERE cod_documento = '.$_REQUEST['stDocumento'].' ' );
            $arDocumentos[$inCount]['ano_documento'] = $_REQUEST['stExercicio'];
            $arDocumentos[$inCount]['mes']           = $_REQUEST['inMes'];
            $arDocumentos[$inCount]['desc_mes']      = strftime('%b',mktime(0,0,0,$_REQUEST['inMes'],1,$_REQUEST['stExercicio']));
            $arDocumentos[$inCount]['situacao']      = ($_REQUEST['stSituacao'] == 'pago') ? true : false;
            $arDocumentos[$inCount]['desc_situacao'] = ($_REQUEST['stSituacao'] == 'pago') ? 'Pago' : 'Não Pago';
            
            if ($_REQUEST['stSituacao'] == 'pago') {
                $arDocumentos[$inCount]['exercicio_empenho'] = $_REQUEST['stExercicioEmpenho'];
                $arDocumentos[$inCount]['cod_entidade'] = $_REQUEST['inCodEntidadeOculto'];
                $arDocumentos[$inCount]['cod_empenho']  = $_REQUEST['inCodigoEmpenho'];
                //$arDocumentos[$inCount]['nom_empenho']  = $rsLista->getCampo('nom_fornecedor');
            }else{
                $arDocumentos[$inCount]['cod_empenho']  = null;
            }
            $stJs .= montaListaDocumentos( $arDocumentos );
            $stJs .= "$('hdnId').value = '';";
            $stJs .= "$('stDocumento').selectedIndex = 0;";
            $stJs .= "$('stExercicio').value = '".Sessao::getExercicio()."';";
            $stJs .= "$('inMes').selectedIndex = 0;";
            $stJs .= "$('stSituacao1').checked = false;";
            $stJs .= "$('stSituacao2').checked = false;";
            $stJs .= "$('incluiDocumento').value = 'Incluir';";
            $stJs .= "$('incluiDocumento').setAttribute( 'onclick','montaParametrosGET(\'incluirDocumento\',\'stDocumento,stExercicio,inMes,stSituacao,stExercicioEmpenho,inCodEntidadeOculto,inCodigoEmpenho\');');";
            //$stJs .= "$('incluiDocumento').setAttribute( 'onclick','montaParametrosGET(\'incluirDocumento\',\'stDocumento,stExercicio,inMes,stSituacao,stExercicioEmpenho,inCodEntidade,inCodigoEmpenho,stNomFornecedor\');');";
            $stJs .= "$('spnEmpenho').innerHTML = ''; ";

            Sessao::write('arDocumentos' , $arDocumentos);

            //se estivesse excluido, remove das excluidas
            if ( count( Sessao::read('arDocumentosExcluidos') ) > 0 ) {
                foreach ( Sessao::read('arDocumentosExcluidos') AS $arTemp ) {
                    if ($arTemp['cod_documento'] != $_REQUEST['stDocumento'] OR $arTemp['ano_exercicio'] != $_REQUEST['stExercicio']) {
                        $arAux[] = $arTemp;
                    }
                }
            }
            Sessao::write('arDocumentosExcluidos' , $arAux);
        } else {
            $stJs .= "alertaAviso('".$stMensagem."','frm','erro','".Sessao::getId()."'); \n";
        }

        break;

    case 'excluirDocumento' :
        $arAux = array();
        $arDocumentosExcluidos = Sessao::read('arDocumentosExcluidos');
        foreach ( Sessao::read('arDocumentos') AS $arTemp ) {
            if ($arTemp['id'] !=  $_REQUEST['id']) {
                $arAux[] = $arTemp;
            } else {
                $inCount = count($arDocumentosExcluidos);
                $arDocumentosExcluidos[$inCount]['cod_documento'] = $arTemp['cod_documento'];
                $arDocumentosExcluidos[$inCount]['ano_documento'] = $arTemp['ano_documento'];
            }
        }
        Sessao::write('arDocumentosExcluidos' , $arDocumentosExcluidos);
        Sessao::write('arDocumentos' , $arAux);
        $stJs .= montaListaDocumentos( Sessao::read('arDocumentos') );
        break;

    case 'limparDocumentos' :

        $stJs .= "$('hdnId').value = '';";
        $stJs .= "$('stDocumento').selectedIndex = 0;";
        $stJs .= "$('stExercicio').value = '".Sessao::getExercicio()."';";
        $stJs .= "$('inMes').selectedIndex = 0;";
        $stJs .= "$('stSituacao1').checked = false;";
        $stJs .= "$('stSituacao2').checked = false;";
        $stJs .= "$('incluiDocumento').value = 'Incluir';";
        $stJs .= "$('incluiDocumento').setAttribute( 'onclick','montaParametrosGET(\'incluirDocumento\',\'stDocumento,stExercicio,inMes,stSituacao,stExercicioEmpenho,inCodEntidadeOculto,inCodigoEmpenho\');');";
        //$stJs .= "$('incluiDocumento').setAttribute( 'onclick','montaParametrosGET(\'incluirDocumento\',\'stDocumento,stExercicio,inMes,stSituacao,stExercicioEmpenho,inCodEntidade,inCodigoEmpenho,stNomFornecedor\');');";
        $stJs .= "$('spnEmpenho').innerHTML = ''; ";

        break;

    case 'montaAlterar' :

        //seleciona a origem do bem
        if ($_REQUEST['stOrigem'] == 't') {
            $stJs .= "$('stOrigemBemProprio').checked = true;";
        } elseif ($_REQUEST['stOrigem'] == 'f') {
            $stJs .= "$('stOrigemBemTerceiros').checked = true;";
        }

        //recupera os documentos do banco
        $obTFrotaVeiculoDocumento = new TFrotaVeiculoDocumento();
        $obTFrotaVeiculoDocumento->setDado('cod_veiculo',$_REQUEST['inCodVeiculo'] );
        $obTFrotaVeiculoDocumento->recuperaDocumentos( $rsDocumentos );
        
        //monta a lista

        $arDocumentos = Sessao::read('arDocumentos');
        while ( !$rsDocumentos->eof() ) {
            $inCount = count($arDocumentos);
            $arDocumentos[$inCount]['id']            = $inCount + 1;
            $arDocumentos[$inCount]['cod_documento'] = $rsDocumentos->getCampo('cod_documento');
            $arDocumentos[$inCount]['nom_documento'] = $rsDocumentos->getCampo('nom_documento');
            $arDocumentos[$inCount]['mes']           = $rsDocumentos->getCampo('mes');
            $arDocumentos[$inCount]['ano_documento'] = $rsDocumentos->getCampo('exercicio');
            $arDocumentos[$inCount]['desc_mes']      = strftime('%b',mktime(0,0,0,$rsDocumentos->getCampo('mes'),1,$rsDocumentos->getCampo('exercicio')));
            $arDocumentos[$inCount]['situacao']      = ($rsDocumentos->getCampo('situacao') == 'pago') ? true : false;
            $arDocumentos[$inCount]['desc_situacao'] = ($rsDocumentos->getCampo('situacao') == 'pago') ? 'Pago' : 'Não Pago';
            if ( $rsDocumentos->getCampo('situacao') == 'pago' ) {
                $arDocumentos[$inCount]['exercicio_empenho']    = $rsDocumentos->getCampo('exercicio_empenho');
                $arDocumentos[$inCount]['cod_entidade'] = $rsDocumentos->getCampo('cod_entidade');
                $arDocumentos[$inCount]['cod_empenho']  = $rsDocumentos->getCampo('cod_empenho');
                $arDocumentos[$inCount]['nom_empenho']  = $rsDocumentos->getCampo('nom_empenho');
            }
            $rsDocumentos->proximo();
        }
        Sessao::write('arDocumentos' , $arDocumentos);
        $stJs .= montaListaDocumentos( $arDocumentos );

        break;

    case 'montaResponsavel' :

        if ($_REQUEST['stOrigem'] != 'proprio') {
            $obForm = new Form();

            //instancia o componente IPopUpCGMVinculado para o responsavel
            $obIPopUpResponsavel = new IPopUpCGMVinculado( $obForm );
            $obIPopUpResponsavel->setTabelaVinculo    ( 'sw_cgm_pessoa_fisica' );
            $obIPopUpResponsavel->setCampoVinculo     ( 'numcgm'               );
            $obIPopUpResponsavel->setNomeVinculo      ( 'Responsável'          );
            $obIPopUpResponsavel->setRotulo           ( 'Responsável'          );
            $obIPopUpResponsavel->setTitle            ( 'Informe o reponsável pelo veículo.' );
            $obIPopUpResponsavel->setName             ( 'stNomResponsavel'       );
            $obIPopUpResponsavel->setId               ( 'stNomResponsavel'       );
            $obIPopUpResponsavel->setValue            ( $_REQUEST['stNomResponsavel'] );
            $obIPopUpResponsavel->obCampoCod->setName ( 'inCodResponsavel'       );
            $obIPopUpResponsavel->obCampoCod->setId   ( 'inCodResponsavel'       );
            $obIPopUpResponsavel->obCampoCod->setValue( $_REQUEST['inCodResponsavel'] );
            $obIPopUpResponsavel->setNull             ( false                    );

            //instancia um componente data para o inicio do responsavel
            $obDtInicio = new Data();
            $obDtInicio->setRotulo('Data de Início');
            $obDtInicio->setTitle('Informe a data de início do responsável.');
            $obDtInicio->setName('dtInicio');
            $obDtInicio->setId('dtInicio');
            $obDtInicio->setNull(false);
            $obDtInicio->setValue( $_REQUEST['dtInicio'] );

            $obFormulario = new Formulario();
            $obFormulario->addTitulo( 'Dados do Responsável' );
            $obFormulario->addComponente( $obIPopUpResponsavel );
            $obFormulario->addComponente( $obDtInicio );
            $obFormulario->montaInnerHTML();

            $stJs = "$('spnResponsavel').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
            $stJs = "$('spnResponsavel').innerHTML = '';";
        }

        break;

    case 'montaPrefixoPlaca' :
        if ($_REQUEST['slTipoVeiculo'] != '') {

            //recupera as informacoes do banco
            $obTFrotaTipoVeiculo = new TFrotaTipoVeiculo();
            $obTFrotaTipoVeiculo->setDado( 'cod_tipo', $_REQUEST['slTipoVeiculo'] );
            $obTFrotaTipoVeiculo->recuperaPorChave( $rsTipoVeiculo );

            //instancia um textbox para o numero da placa
            $obTxtPlaca = new TextBox();
            $obTxtPlaca->setRotulo( 'Placa do Veículo' );
            $obTxtPlaca->setTitle ( 'Informe a placa do veículo.' );
            $obTxtPlaca->setName  ( 'stNumPlaca' );
            if ( $rsTipoVeiculo->getCampo( 'placa' ) == 't' ) {
                $obTxtPlaca->setNull  ( false );
            }
            $obTxtPlaca->obEvento->setOnKeyUp( "mascaraPlacaVeiculo(this);" );
            $obTxtPlaca->obEvento->setOnBlur( "mascaraPlacaVeiculo(this);" );
            $obTxtPlaca->setMaxLength  ( 8 );
            $obTxtPlaca->setValue( $_REQUEST['stNumPlaca'] );

            //instancia textbox para o prefixo
            $obTxtPrefixo = new TextBox();
            $obTxtPrefixo->setRotulo( 'Prefixo' );
            $obTxtPrefixo->setTitle ( 'Informe prefixo do veículo.' );
            $obTxtPrefixo->setName  ( 'stPrefixo' );
            if ( $rsTipoVeiculo->getCampo( 'prefixo' ) == 't' ) {
                $obTxtPrefixo->setNull  ( false );
            }
            $obTxtPrefixo->setSize  ( 15 );
            $obTxtPrefixo->setMaxLength( 15 );
            $obTxtPrefixo->setValue( $_REQUEST['stPrefixo'] );

            //cria um formulario
            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obTxtPlaca );
            $obFormulario->addComponente( $obTxtPrefixo );
            $obFormulario->montaInnerHTML();

            $stJs .= "$('spnPrefixoPlaca').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
            $stJs .= "$('spnPrefixoPlaca').innerHTML = '';";
        }

    break;

    case 'carregarListaInfracao':
        //apresenta lista de infrações do motorista
        $obTFrotaInfracao = new TFrotaInfracao();
        $stFiltro = " WHERE veiculo.cod_veiculo=".$_REQUEST['inCodVeiculo'];
        $obTFrotaInfracao->recuperaInfracao( $rsFrotaInfracao, $stFiltro, ' ORDER BY data_infracao DESC ');

        $obTable = new Table();
        $obTable->setRecordset( $rsFrotaInfracao );
        $obTable->setSummary( 'Lista de Infrações do Motorista' );

        $obTable->Head->addCabecalho( 'Motorista'    , 11 );
        $obTable->Head->addCabecalho( 'Auto Infração', 6  );
        $obTable->Head->addCabecalho( 'Data'         , 6  );
        $obTable->Head->addCabecalho( 'Motivo'       , 17 );
        $obTable->Head->addCabecalho( 'Gravidade'    , 5  );
        $obTable->Head->addCabecalho( 'Pontos'       , 4  );

        $obTable->Body->addCampo( 'nom_cgm'      , 'C' );
        $obTable->Body->addCampo( 'auto_infracao', 'C' );
        $obTable->Body->addCampo( 'data_infracao', 'C' );
        $obTable->Body->addCampo( 'motivo'       , 'C' );
        $obTable->Body->addCampo( 'gravidade'    , 'C' );
        $obTable->Body->addCampo( 'pontos'       , 'C' );

        $obTable->montaHTML( true );

        $obLabel = new Label();
        $obLabel->setName('total_infracao');
        $obLabel->setRotulo('Total de infrações');
        if( $rsFrotaInfracao->getNumLinhas() > 0 ) {
            $obLabel->setValue($rsFrotaInfracao->getNumLinhas());
        } else {
            $obLabel->setValue( 0 );
        }

        $obFormulario = new Formulario;
        $obFormulario->addComponente( $obLabel );
        $obFormulario->montaInnerHTML();

        $stJs.= "$('spnInfracao').innerHTML='".$obTable->getHtml().$obFormulario->getHTML()."';";
    break;
}

echo $stJs;
