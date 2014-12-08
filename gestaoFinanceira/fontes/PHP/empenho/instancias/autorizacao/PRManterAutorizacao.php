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
    * Página de Processamento de Autorização
    * Data de Criação   : 01/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 32545 $
    $Name$
    $Autor:$
    $Date: 2008-01-02 08:44:54 -0200 (Qua, 02 Jan 2008) $

    * Casos de uso: uc-02.03.02
                    uc-02.01.08
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stCaminho = CAM_GF_EMP_INSTANCIAS."autorizacao/OCGeraRelatorioAutorizacao.php";

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

$obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;

//Atributos Dinâmicos
foreach ($arChave as $key=>$value) {
    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
    $inCodAtributo = $arChaves[0];
    if ( is_array($value) ) {
        $value = implode(",",$value);
    }
    $obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
}

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$obErro = new Erro;

switch ($stAcao) {
    case "material":
    case "incluir":
        //valida a utilização da rotina de encerramento do mês contábil
        $arDtAutorizacao = explode('/', $_POST['stDtAutorizacao']);
        $boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
        include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
        $obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
        $obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
        $obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
        $obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');
        
        if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
            SistemaLegado::executaFrameOculto(" window.parent.frames['telaPrincipal'].document.getElementById('Ok').disabled = false; ");
            SistemaLegado::exibeAviso(urlencode("Mês da Autorização encerrado!"),"n_incluir","erro");
            exit;
        }
        
        if (( $_REQUEST['inCodCategoria'] == 2 || $_REQUEST['inCodCategoria'] == 3) && (!$_POST['inCodContrapartida']) ) {
            $obErro->setDescricao("Campo Contrapartida inválido!");
        }
        
        $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento( $stFormaExecucao );
        if ($_POST['inCodDespesa']) {
            if ($stFormaExecucao==1 and (!$_REQUEST['stCodClassificacao'])) {
                $obErro->setDescricao("Desdobramento não informado!");
            }
            if ( !$obErro->ocorreu() ) {
               $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST["inCodDespesa"] );
               $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
               $obREmpenhoAutorizacaoEmpenho->consultaSaldoAnterior( $nuSaldoDotacao );
               if ($_REQUEST['nuVlTotalAutorizacao'] > $nuSaldoDotacao) {
                   $obErro->setDescricao("O Saldo da Dotação é menor que o Valor Total da Autorização!");
               }
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( 1 );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $_POST['inCodDespesa'] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $_REQUEST['stCodClassificacao'] );
            $obREmpenhoAutorizacaoEmpenho->obRCGM->setNumCGM( $_POST['inCodFornecedor'] );
            $obREmpenhoAutorizacaoEmpenho->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->setCodHistorico( $_POST['inCodHistorico'] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeInicial( $_POST['stDtAutorizacao'] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeFinal( $_POST['stDtValidadeFinal'] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setDtInclusao( $_POST['stDtAutorizacao'] );
            $obREmpenhoAutorizacaoEmpenho->setDescricao( $_POST['stDescricao'] );
            $obREmpenhoAutorizacaoEmpenho->setDtAutorizacao( $_POST['stDtAutorizacao'] );
            $nuVlReserva = str_replace('.','',$_POST['nuVlReserva'] );
            $nuVlReserva = str_replace(',','.',$nuVlReserva );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setVlReserva( $nuVlReserva );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $_POST['inCodOrgao']);
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($_POST['inCodUnidadeOrcamento']);
            $obREmpenhoAutorizacaoEmpenho->setCodCategoria($_REQUEST['inCodCategoria']);
            
            if ($_REQUEST['inCodCategoria'] == 2 || $_REQUEST['inCodCategoria'] == 3) {
                $obREmpenhoAutorizacaoEmpenho->obTEmpenhoContrapartidaAutorizacao->setDado('conta_contrapartida',$_REQUEST['inCodContrapartida']);
            }
            
            $arItens = Sessao::read('arItens');
            if ( sizeof( $arItens ) ) {
                foreach ($arItens as $arItemPreEmpenho) {
                    $obREmpenhoAutorizacaoEmpenho->addItemPreEmpenho();
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNumItem    ( $arItemPreEmpenho["num_item"     ] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setQuantidade ( $arItemPreEmpenho['quantidade'   ] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomUnidade ( $arItemPreEmpenho["nom_unidade"  ] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setValorTotal ( $arItemPreEmpenho["vl_total"     ] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomItem    ( $arItemPreEmpenho["nom_item"     ] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setComplemento( $arItemPreEmpenho["complemento"  ] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodMaterial( $arItemPreEmpenho["cod_material" ] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->setCodUnidade( $arItemPreEmpenho['cod_unidade'] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->obRGrandeza->setCodGrandeza( $arItemPreEmpenho["cod_grandeza"] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->consultar($rsUnidade);
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setSiglaUnidade( $rsUnidade->getCampo('simbolo') );
                    if($_REQUEST['stTipoItem']=='Catalogo'){
                        $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodItemPreEmp( $arItemPreEmpenho["cod_item"    ] );
                    }
                }
            } else {
                $obErro->setDescricao( "É necessário cadastrar pelo menos um Item" );
            }
            
            if ( !$obErro->ocorreu() ) {
                $obErro = $obREmpenhoAutorizacaoEmpenho->incluir($boTransacao);
            }
            
            if ( !$obErro->ocorreu() ) {
                
                SistemaLegado::alertaAviso($pgForm.'?&stAcao='.$stAcao, $obREmpenhoAutorizacaoEmpenho->getCodAutorizacao()."/".Sessao::getExercicio(), 'incluir', "aviso", Sessao::getId(), "../");
                
                /* Salvar assinaturas configuráveis se houverem */
                $arAssinaturas = Sessao::read('assinaturas');
                    if (isset($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0) {
            include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenhoAssinatura.class.php" );
            $arAssinatura = $arAssinaturas['selecionadas'];
            
            $obTEmpenhoAutorizacaoEmpenhoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'exercicio', $obREmpenhoAutorizacaoEmpenho->stExercicio );
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'cod_entidade', $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'cod_autorizacao', $obREmpenhoAutorizacaoEmpenho->getCodAutorizacao() );
                        $arPapel = $obTEmpenhoAutorizacaoEmpenhoAssinatura->arrayPapel();
                        
            foreach ($arAssinatura as $arAssina) {
                        // As assinaturas quando carregam os dados trazem no papel o num_assina, porém quando tu seleciona qualquer um deles
                        // no papel fica a descrição dele, e não o numero, por isso da verificação com o is_string()
                            $inNumAssina = 1;
                            if (isset($arAssina['papel'])) {
                                if (is_string($arAssina['papel'])) {
                                    $inNumAssina = $arPapel[$stPapel];
                                } else {
                                    $inNumAssina = $arAssina['papel'];
                                }
                        }
                            $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('num_assinatura', $inNumAssina);
                            $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('numcgm', $arAssina['inCGM']);
                            $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('cargo', $arAssina['stCargo']);
                            $obErro = $obTEmpenhoAutorizacaoEmpenhoAssinatura->inclusao( $boTransacao );
            }
            unset($obTEmpenhoAutorizacaoEmpenhoAssinatura);
            // Limpa Sessao->assinaturas
            $arAssinaturas = array( 'disponiveis' => array(), 'papeis' => array(), 'selecionadas' => array() );
                        Sessao::write('assinaturas', $arAssinaturas);
            }
            
                $stCampos  = $stCaminho."?inCodAutorizacao=".$obREmpenhoAutorizacaoEmpenho->getCodAutorizacao().
                "&inCodPreEmpenho="  .$obREmpenhoAutorizacaoEmpenho->getCodPreEmpenho()."&inCodEntidade=".$_POST['inCodEntidade']."&stExercicio=".Sessao::getExercicio().
                "&inCodDespesa=".$_POST['inCodDespesa']."&stDtAutorizacao=".$obREmpenhoAutorizacaoEmpenho->getDtAutorizacao() . "&stAcao=autorizacao";
                echo "<script>window.location.href='".$stCampos."';</script>";
                
            } else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        } else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            
    break;
    case "alterar":
        //valida a utilização da rotina de encerramento do mês contábil
        $arDtAutorizacao = explode('/', $_REQUEST['stDtInclusao']);
        $boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
        include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
        $obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
        $obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
        $obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
        $obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');
        
        if ($_REQUEST['stCodClassificacao'] == '' && $_REQUEST['stCodEstrutural'] != ''){
            $stCodClassificacao = $_REQUEST['stCodEstrutural'];
        } else {
            $stCodClassificacao = $_REQUEST['stCodClassificacao'];
        }
        
        if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
            SistemaLegado::executaFrameOculto(" window.parent.frames['telaPrincipal'].document.getElementById('Ok').disabled = false; ");
            SistemaLegado::exibeAviso(urlencode("Mês da Autorização encerrado!"),"n_incluir","erro");
            exit;
        }
        
        if (( $_REQUEST['inCodCategoria'] == 2 || $_REQUEST['inCodCategoria'] == 3) && (!$_REQUEST['inCodContrapartida']) ) {
            $obErro->setDescricao( "Contrapartida não informada!" );
        }
        
        $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento( $stFormaExecucao );
        if ($_REQUEST['inCodDespesa']) {
            if ($stFormaExecucao==1 and (!$stCodClassificacao)) {
                $obErro->setDescricao("Desdobramento não informado!");
            }
            if ( !$obErro->ocorreu() ) {
               if ($_REQUEST['nuVlTotalAutorizacao'] > $_REQUEST['flVlSaldoDotacao']) {
                   $obErro->setDescricao("O Saldo da Dotação é menor que o Valor Total da Autorização!");
               }
            }
        }
        
        if (isset($sessao->assinaturas['selecionadas'])) {
            $arSelecionadas = $sessao->assinaturas['selecionadas'];
            foreach ($arSelecionadas as $arAssinaturas) {
                if (!isset($arAssinaturas['papel'])) {
                    $obErro->setDescricao("Selecione o Papel de ".$arAssinaturas['stNomCGM']." na lista de assinaturas.");
                    break;
                }
            }
        }
        
        if ( !$obErro->ocorreu() ) {
            $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( 0 );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST['inCodDespesa'] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $stCodClassificacao );
            $obREmpenhoAutorizacaoEmpenho->obRCGM->setNumCGM( $_REQUEST['inCodFornecedor'] );
            $obREmpenhoAutorizacaoEmpenho->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->setCodHistorico( $_REQUEST['inCodHistorico'] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeInicial( $_REQUEST['stDtValidadeInicial'] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeFinal( $_REQUEST['stDtValidadeFinal'] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setDtInclusao( $_REQUEST['stDtInclusao'] );
            $obREmpenhoAutorizacaoEmpenho->setDescricao( $_REQUEST['stDescricao'] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setVlReserva( $_REQUEST['nuVlReserva'] );
            
             if ($request->get('inCodOrgao') == '' && $request->get('hdnCodOrgao') != ''){
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($request->get('hdnCodOrgao'));
            } else {
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST['inCodOrgao']);
            }
            
            if ($request->get('inCodUnidadeOrcamento') == '' && $request->get('hdnCodUnidade') != ''){
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($request->get('hdnCodUnidade'));
            } else {
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($_REQUEST['inCodUnidadeOrcamento']);
            }
            
            $obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $_REQUEST['inCodAutorizacao'] );
            $obREmpenhoAutorizacaoEmpenho->setCodPreEmpenho( $_REQUEST['inCodPreEmpenho'] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setCodReserva( $_REQUEST['inCodReserva'] );
            $obREmpenhoAutorizacaoEmpenho->setCodCategoria($_REQUEST['inCodCategoria']);
            
            if ($_REQUEST['inCodCategoria'] == 2 || $_REQUEST['inCodCategoria'] == 3) {
                $obREmpenhoAutorizacaoEmpenho->obTEmpenhoContrapartidaAutorizacao->setDado('conta_contrapartida',$_REQUEST['inCodContrapartida']);
            }
            
            $arItens = Sessao::read('arItens');
            if ( sizeof( $arItens ) ) {
                foreach ($arItens as $arItemPreEmpenho) {
                    $obREmpenhoAutorizacaoEmpenho->addItemPreEmpenho( $this );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNumItem    ( $arItemPreEmpenho["num_item"    ] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setQuantidade ( $arItemPreEmpenho["quantidade"  ] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomUnidade ( $arItemPreEmpenho["nom_unidade" ] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setValorTotal ( $arItemPreEmpenho["vl_total"    ] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomItem    ( $arItemPreEmpenho["nom_item"    ] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setComplemento( $arItemPreEmpenho["complemento" ] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodMaterial( $arItemPreEmpenho["cod_material"] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->setCodUnidade( $arItemPreEmpenho['cod_unidade'] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->obRGrandeza->setCodGrandeza( $arItemPreEmpenho["cod_grandeza"] );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->consultar($rsUnidade);
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setSiglaUnidade( $rsUnidade->getCampo('simbolo') );
                    
                    if ($_REQUEST['stTipoItem']=='Catalogo'){
                        $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodItemPreEmp( $arItemPreEmpenho["cod_item"] );
                    }
                }
                
            } else {
                $obErro->setDescricao( "É necessário cadastrar pelo menos um Item" );
            }
            
            if ( !$obErro->ocorreu() ) {
                $obErro = $obREmpenhoAutorizacaoEmpenho->alterar();
            }
            
            /* Excluir Assinaturas vinculadas ao documento */
            if ( !$obErro->ocorreu() ) {
                include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenhoAssinatura.class.php" );
                /* Montar um RecordSet com todas as assinaturas vinculadas ao documento na tabela correspondente */
                $obTAutorizacaoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
                $obTAutorizacaoAssinatura->setDado( 'exercicio', $obREmpenhoAutorizacaoEmpenho->stExercicio );
                $obTAutorizacaoAssinatura->setDado( 'cod_entidade', $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                $obTAutorizacaoAssinatura->setDado( 'cod_autorizacao', $obREmpenhoAutorizacaoEmpenho->getCodAutorizacao() );
                $rsRecordSet = new RecordSet;
                $obTAutorizacaoAssinatura->recuperaAssinaturasAutorizacao( $rsRecordSet, '', '', '' );
                /* Excluir um por um os Itens das Assinaturas */
                $obTEAutorizacaoEmpAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
                $obTEAutorizacaoEmpAssinatura->setDado( 'exercicio', $obTAutorizacaoAssinatura->getDado('exercicio') );
                $obTEAutorizacaoEmpAssinatura->setDado( 'cod_entidade', $obTAutorizacaoAssinatura->getDado('cod_entidade') );
                $obTEAutorizacaoEmpAssinatura->setDado( 'cod_autorizacao', $obTAutorizacaoAssinatura->getDado('cod_autorizacao') );
                
                while ($rsRecordSet->each() ) {
                    $arAssinaturaBanco = $rsRecordSet->getObjeto();
                    $obTEAutorizacaoEmpAssinatura->setDado( 'num_assinatura', $arAssinaturaBanco['num_assinatura'] );
                    $obErro = $obTEAutorizacaoEmpAssinatura->exclusao();
                }
            }
            
            /* Salvar assinaturas configuráveis se houverem */
            if ( !$obErro->ocorreu() ) {
                include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenhoAssinatura.class.php" );
                $arAssinaturas = Sessao::read('assinaturas');
                if ( isset($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0 ) {
                    $arAssinatura = $arAssinaturas['selecionadas'];
                    // Array configurado de acordo com o lay-out do documento que será emitido (ver documento impresso)
                    $arPapel = array( 'autorizo'=>1, 'autorizoempenho'=>2 );

                    $obTEmpenhoAutorizacaoEmpenhoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'exercicio', $obREmpenhoAutorizacaoEmpenho->stExercicio );
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'cod_entidade', $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'cod_autorizacao', $obREmpenhoAutorizacaoEmpenho->getCodAutorizacao() );

                    foreach ($arAssinatura as $arAssina) {
                        $stPapel = (isset($arAssina['papel'])) ? $arAssina['papel'] : '';
                        $inNumAssina = (isset($arPapel[$stPapel])) ? $arPapel[$stPapel] : 1;
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'num_assinatura', $inNumAssina );
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'numcgm', $arAssina['inCGM'] );
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'cargo', $arAssina['stCargo'] );
                        $obErro = $obTEmpenhoAutorizacaoEmpenhoAssinatura->inclusao( $boTransacao );
                    }
                }
                // Limpa Sessao->assinaturas
                $arAssinaturas = array( 'disponiveis'=>array(), 'papeis'=>array(), 'selecionadas'=>array() );
                Sessao::write('assinaturas', $arAssinaturas);
            }
            
            if ( !$obErro->ocorreu() ) {
                $stFiltro = "";
                $arFiltro = Sessao::read('filtro');
                foreach ($arFiltro as $stCampo => $stValor) {
                    $stFiltro .= $stCampo."=".@urlencode( $stValor )."&";
                }
                
                $stFiltro .= "pg=".Sessao::read('pg')."&";
                $stFiltro .= "pos=".Sessao::read('pos')."&";
                $stFiltro .= "stAcao=".$_REQUEST['stAcao'];
                
                if ( !$obErro->ocorreu() ) {
                    SistemaLegado::alertaAviso($pgList."?".$stFiltro, $_POST['inCodAutorizacao']."/".Sessao::getExercicio(), "alterar", "aviso", Sessao::getId(), "../");
                    
                    $stCampos  = $stCaminho."?inCodAutorizacao=".$obREmpenhoAutorizacaoEmpenho->getCodAutorizacao().
                    "&inCodPreEmpenho="  .$obREmpenhoAutorizacaoEmpenho->getCodPreEmpenho()."&inCodEntidade=".$_POST['inCodEntidade']."&inCodDespesa=".$_POST['inCodDespesa']."&stAcao=autorizacao&stExercicio=".Sessao::getExercicio();
                    
                    echo "<script>window.location.href='".$stCampos."';</script>";
                } else {
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            }
        } else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    break;
    case "anular":
        //valida a utilização da rotina de encerramento do mês contábil
        $arDtAutorizacao = explode('/', date('d/m/').Sessao::getExercicio());
        $boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
        include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
        $obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
        $obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
        $obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
        $obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

        if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
            SistemaLegado::executaFrameOculto(" window.parent.frames['telaPrincipal'].document.getElementById('Ok').disabled = false; ");
            SistemaLegado::exibeAviso(urlencode("Mês da Autorização encerrado!"),"n_incluir","erro");
            exit;
        }

        $obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $_POST['inCodAutorizacao'] );
        $obREmpenhoAutorizacaoEmpenho->setCodPreEmpenho( $_POST['inCodPreEmpenho'] );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setCodReserva( $_POST['inCodReserva'] );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setCodDespesa( $_POST['inCodDespesa'] );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $_POST['inCodDespesa'] );
        $obREmpenhoAutorizacaoEmpenho->setExercicio( $_POST['stExercicio'] );
        $obREmpenhoAutorizacaoEmpenho->setMotivoAnulacao( $_REQUEST['stMotivo']." - Autorização de Empenho: ".$_POST['inCodAutorizacao']."/".Sessao::getExercicio() );
        $obREmpenhoAutorizacaoEmpenho->setDtAnulacao( date('d/m/').Sessao::getExercicio() );
        $obErro = $obREmpenhoAutorizacaoEmpenho->anular();

         $stFiltro = "";
         $arFiltro = Sessao::read('filtro');
         foreach ($arFiltro as $stCampo => $stValor) {
            $stFiltro .= $stCampo."=".@urlencode( $stValor )."&";
         }
         $stFiltro .= "pg=".Sessao::read('pg')."&";
         $stFiltro .= "pos=".Sessao::read('pos')."&";
         $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

         if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso( $pgList."?stAcao=anular&".$stFiltro, $_REQUEST['inCodAutorizacao'] .'/'.$_POST['stExercicio'],"excluir","aviso",Sessao::getId(),"../");

            $stCampos  = $stCaminho."?inCodAutorizacao=".$obREmpenhoAutorizacaoEmpenho->getCodAutorizacao().
            "&inCodPreEmpenho="  .$obREmpenhoAutorizacaoEmpenho->getCodPreEmpenho()."&inCodEntidade=".$_POST['inCodEntidade']."&inCodDespesa=".$_POST['inCodDespesa']."&stExercicio=".$_POST['stExercicio']."&stAcao=anulacao";

            echo "<script>window.location.href='".$stCampos."';</script>";

         } else {
             SistemaLegado::alertaAviso( $pgList."?stAcao=anular&".$stFiltro, urlencode($obErro->getDescricao()), "n_excluir","erro",Sessao::getId(),"../" );
         }
    break;
}

if ($obErro->ocorreu()) SistemaLegado::executaFrameOculto(" window.parent.frames['telaPrincipal'].document.getElementById('Ok').disabled = false; ");
?>
