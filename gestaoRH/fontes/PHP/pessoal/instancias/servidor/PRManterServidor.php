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
inCategoriaCertificado

* Página de Processamento de Pessoal Servidor
* Data de Criação   : 21/12/2004

* @author Analista: Leandro Oliveira.
* @author Desenvolvedor: Rafael Almeida

* @ignore

$Revision: 32866 $
$Name$
$Author: souzadl $
$Date: 2008-03-24 11:59:05 -0300 (Seg, 24 Mar 2008) $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
include_once(CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php");
include_once(CAM_GA_NORMAS_NEGOCIO."RNorma.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");

$stAcao = $request->get('stAcao');
$inAba  = $_REQUEST["inAba"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterServidor";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao&inAba=$inAba";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao&inAba=$inAba";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao&inAba=$inAba";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao&inAba=$inAba";
$pgOcul = "OC".$stPrograma.".php";

$obRPessoalServidor = new RPessoalServidor;
$obRConfiguracaoPessoal = new RConfiguracaoPessoal;

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

$obRConfiguracaoPessoal->Consultar( $boTransacao );
$obErro = new Erro;

include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php");
$obTCGM = new TCGM;
$stFiltro = " AND cgm.numcgm = ".$_REQUEST["inNumCGM"];
$obTCGM->recuperaRelacionamentoSintetico( $rsCGM, $stFiltro, '', $boTransacao );

if (trim($stAcao)=="alterar_servidor") {
    // Quando incluir mais de uma matrícula para o mesmo CGM voltar para a tela de inclusão
    $_POST['actVoltar'] = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=incluir";
}

switch ($stAcao) {
    case "incluir":
        $obTransacao = new Transacao;
        $boFlagTransacao = false;
        $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $nomeFoto = Sessao::read('FOTO_NAME');
        if ($nomeFoto) {
            //NOME DO ARQUIVO DA MINIATURA
            $imagem_gerada = explode(".", $nomeFoto);
            $nome_foto = sistemaLegado::getmicrotime() . "_" . $imagem_gerada[0];
            $imagem_gerada = CAM_GRH_PES_ANEXOS. $nome_foto . "_mini.jpg";
            $nome_foto = $nome_foto ."_mini.jpg";
            $stFoto = fopen($imagem_gerada,"w+");
            fwrite ($stFoto, Sessao::read('FOTO_BIN'));
            fclose($stFoto);
        }

        $obRPessoalServidor->recuperaTodosRaca( $rsRaca, $boTransacao );

        $obRPessoalServidor->setCodUF                          ( $_POST['inCodUF']);
        $obRPessoalServidor->setCodMunicipio                   ( $_POST['inCodMunicipio'] );
        $obRPessoalServidor->setCodEstadoCivil                 ( $_POST['inCodEstadoCivil']);
        $obRPessoalServidor->setCodRais                        ( $_POST['inCodRaca']);
        $obRPessoalServidor->setCodRaca                        ( $rsRaca->getCampo('cod_raca') );
        $obRPessoalServidor->obRPessoalCID->setCodCID          ( $_POST['inCodCID']);
        $obRPessoalServidor->setCodEdital                      ( '0' );
        $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM     ( $_POST['inNumCGM'] );
        $obRPessoalServidor->obRCGMPessoaFisica->setCPF        ( $_POST['stCPF'] );
        $obRPessoalServidor->setDataNascimento                 ( $_POST['stDataNascimento'] );
        $obRPessoalServidor->setNomePai                        ( $_POST['stNomePai'] );
        $obRPessoalServidor->setNomeMae                        ( $_POST['stNomeMae'] );
        $obRPessoalServidor->obRCGMPessoaFisicaConjuge->setNumCgm( $_POST['inCGMConjuge'] );

        //dados aba documentacao
        if (!$obErro->ocorreu() && !checkPIS($_POST['stPisPasep'], false)) {
            $obErro->setDescricao("Campo PIS/PASEP da guia Documentação é inválido(".$_POST['stPisPasep'].").");
        }

        $obRPessoalServidor->setPisPasep                       ( $_POST['stPisPasep']                  );
        $obRPessoalServidor->setDataPisPasep                   ( $_POST['dtCadastroPis']               );
        $obRPessoalServidor->setCarteiraReservista             ( $_POST['stCertificadoReservista']     );
        $obRPessoalServidor->setCategoriaReservista            ( $_POST['inCategoriaCertificado']      );
        $obRPessoalServidor->setOrigemReservista               ( $_POST['inOrgaoExpedidorCertificado'] );
        $obRPessoalServidor->setNrTituloEleitor                ( $_POST['inTituloEleitor']             );
        $obRPessoalServidor->setZonaTitulo                     ( $_POST['inZonaTitulo']                );
        $obRPessoalServidor->setSecaoTitulo                    ( $_POST['inSecaoTitulo']               );
        $obRPessoalServidor->setCaminhoFoto                    ( $nome_foto                            );

        $arrCTPS = Sessao::read('CTPS');
        if (is_array($arrCTPS) ) {
            foreach ($arrCTPS as $arCTPS) {
                $obRPessoalServidor->addRPessoalCTPS();
                $obRPessoalServidor->roRPessoalCTPS->setNumero          ( $arCTPS['inNumeroCTPS']           );
                $obRPessoalServidor->roRPessoalCTPS->setOrgaoExpedidor  ( $arCTPS['stOrgaoExpedidorCTPS']   );
                $obRPessoalServidor->roRPessoalCTPS->setSerie           ( $arCTPS['stSerieCTPS']            );
                $obRPessoalServidor->roRPessoalCTPS->setEmissao         ( $arCTPS['dtDataCTPS']             );
                $obRPessoalServidor->roRPessoalCTPS->setUfCTPS          ( $arCTPS['inCodUF']                );
            }
        }
        if ( !$obErro->ocorreu() and $stAcao != 'incluir' ) {
            // verificando se a data de alteração da função é maior que a data de nomeação
            if ($_POST['stContagemInicial'] == 'dtNomeacao') {
                if ( compData($_REQUEST['dtDataAlteracaoFuncao'] , $_POST['dtDataNomeacao'] ) == 2 ) {
                    $obErro->setDescricao("A data de Alteração da função deve ser maior ou igual a Data de Nomeação!");
                }
            } else {
                $dtDataAlteracaoFuncao = ( $_REQUEST["dtDataAlteracaoFuncao"] != "" ) ? $_REQUEST["dtDataAlteracaoFuncao"] : $_REQUEST["hdnDataAlteracaoFuncao"];
                if ( compData( $dtDataAlteracaoFuncao, $_POST['dtDataPosse']) == 2 ) {
                     $obErro->setDescricao("A data de Alteração da função deve ser maior ou igual a Data da Posse!");
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            //Dados aba contrato
            //Informações contratuais
            $obRPessoalServidor->addContratoServidor();

            if ( $obRConfiguracaoPessoal->getGeracaoRegistro() == 'A' ) {

                include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                               );
                $obIContratoDigitoVerificadorAutomatico = new IContratoDigitoVerificador("", false, false, $boTransacao);
                $obIContratoDigitoVerificadorAutomatico->obRPessoalContrato->proximoRegistro($boTransacao);
                $inContratoAutomatico = $obIContratoDigitoVerificadorAutomatico->obRPessoalContrato->getRegistro();

                $inContrato =  explode("-",$inContratoAutomatico);

            } else {
                $inContrato =  explode("-",$_POST[inContrato]);
            }

            //Verifica Norma
            $arCodNorma = explode("/",$_POST['stCodNorma']);
            if (count($arCodNorma)>0) {
                $stNumNorma = ltrim($arCodNorma[0],'0');
                if ($stNumNorma == "") {
                    $stNumNorma = "0";
                }
                $obRNorma = new RNorma();
                $obRNorma->setNumNorma( $stNumNorma );
                $obRNorma->setExercicio( $arCodNorma[1] );
                $obRNorma->listar($rsNorma, $boTransacao);
                $stCodNorma = $rsNorma->getCampo('cod_norma');
            }

            $obRPessoalServidor->roUltimoContratoServidor->setRegistro                                    ( $inContrato[0]                      );
            $obRPessoalServidor->roUltimoContratoServidor->setNroCartaoPonto                              ( $_POST[inCartaoPonto]               );
            $obRPessoalServidor->roUltimoContratoServidor->setAtivo                                       ( "true"                              );
            $obRPessoalServidor->roUltimoContratoServidor->setNomeacao                                    ( $_POST[dtDataNomeacao]              );
            $obRPessoalServidor->roUltimoContratoServidor->obRNorma->setCodNorma                          ( $stCodNorma                         );
            $obRPessoalServidor->roUltimoContratoServidor->setPosse                                       ( $_POST[dtDataPosse]                 );
            $obRPessoalServidor->roUltimoContratoServidor->setAdmissao                                    ( $_POST['dtAdmissao']                );
            $obRPessoalServidor->roUltimoContratoServidor->setValidadeExameMedico                         ( $_POST[dtValidadeExameMedico]       );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoAdmissao->setCodTipoAdmissao     ( $_POST[inCodTipoAdmissao]           );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalVinculoEmpregaticio->setCodVinculoEmpregaticio( $_POST[inCodVinculoEmpregaticio]  );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCategoria->setCodCategoria           ( $_POST[inCodCategoria]              );
            $obRPessoalServidor->roUltimoContratoServidor->setCodConselho                                 ( $_POST['inCodConselho']             );
            $obRPessoalServidor->roUltimoContratoServidor->setNroConselho                                 ( $_POST['inNumeroConselho']          );
            $obRPessoalServidor->roUltimoContratoServidor->setValidadeConselho                            ( $_POST['dtDataValidadeConselho']    );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->setCodOcorrencia         ( $_POST[stNumClassificacao]          );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->setCodGrade            ( $_POST['inCodGradeHorario']         );
            $obRPessoalServidor->roUltimoContratoServidor->setVigenciaSalario                             ( $_POST['dtVigenciaSalario']         );

            //Informações do cargo
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->setCodRegime                 ( $_POST[inCodRegime]                 );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $_POST[inCodSubDivisao]);
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo                   ( $_POST[inCodCargo]                  );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->addEspecialidadeSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $_POST[inCodEspecialidadeCargo]   );

            //Informações da Função
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegimeFuncao->setCodRegime         ( $_POST[inCodRegimeFuncao]         );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->setCodCargo           ( $_POST[inCodFuncao]               );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addEspecialidade();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->addEspecialidadeSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->setCodEspecialidade( $_POST[inCodEspecialidadeFuncao]  );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addCargoSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $_POST[inCodSubDivisaoFuncao]);

            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioBancoSalario->setNumBanco        ( $_POST[inCodBancoSalario]         );
            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaSalario->setCodAgencia    ( $_POST[inCodAgenciaSalario]       );

            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioBancoFGTS->setNumBanco           ( $_POST[inCodBancoFGTS]            );
            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaFGTS->setCodAgencia       ( $_POST[inCodAgenciaFGTS]          );

            $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoSindicato->obRCGM->setNumCGM( $_POST['inNumCGMSindicato']       );

            $obRPessoalServidor->roUltimoContratoServidor->obROrganogramaOrgao->setCodOrgao           ( $_POST["hdnUltimoOrgaoSelecionado"] );

            $obRPessoalServidor->roUltimoContratoServidor->obROrganogramaLocal->setCodLocal               ( $_POST[inCodLocal]                );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalFormaPagamento->setCodFormaPagamento ( $_POST[inCodFormaPagamento]       );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoPagamento->setCodTipoPagamento   ( $_POST[inCodTipoPagamento]        );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoSalario->setCodTipoSalario       ( $_POST[inCodTipoSalario]          );
            $obRPessoalServidor->roUltimoContratoServidor->setDataBase                                    ( $_POST[dtDataBase]                );
            $obRPessoalServidor->roUltimoContratoServidor->setOpcaoFgts                                   ( $_POST[dtDataFGTS]                );
            $obRPessoalServidor->roUltimoContratoServidor->setContaCorrenteFgts                           ( $_POST[inContaCreditoFGTS]        );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->setCodPadrao( $_POST[inCodPadrao]);
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->addNivelPadrao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setCodNivelPadrao( $_POST[inCodProgressao] );
            $obRPessoalServidor->roUltimoContratoServidor->setSalario                                     ( $_POST[inSalario]                 );
            $obRPessoalServidor->roUltimoContratoServidor->setHrMensal                                    ( $_POST[stHorasMensais]            );
            $obRPessoalServidor->roUltimoContratoServidor->setHrSemanal                                   ( $_POST[stHorasSemanais]           );
            $obRPessoalServidor->roUltimoContratoServidor->setInicioProgressao                            ( $_POST[dtDataProgressao]        );
            $obRPessoalServidor->roUltimoContratoServidor->setContaCorrenteSalario                        ( $_POST[inContaSalario]            );
            $obRPessoalServidor->roUltimoContratoServidor->setAdiantamento                                ( $_POST[boAdiantamento]            );
            $obRPessoalServidor->roUltimoContratoServidor->setAdiantamento                                ( $_POST[boAdiantamento]      );
            
            //dados aba dependente
            $arDependentes = Sessao::read('DEPENDENTE');
            if ( is_array($arDependentes) ) {
                for ($inCount=0; $inCount<count($arDependentes); $inCount++) {
                    $arDependente = $arDependentes[$inCount];
                    $obRPessoalServidor->addRPessoalDependente();

                    $obRPessoalServidor->roRPessoalDependente->obRCGMPessoaFisica->setNumCgm            ( $arDependente['inCGMDependente']                                    );
                    $obRPessoalServidor->roRPessoalDependente->obRCGMPessoaFisica->setDataNascimento    ( $arDependente['stDataNascimentoDependente']                         );

                    $obRPessoalServidor->roRPessoalDependente->setCodGrau                               ( $arDependente['stGrauParentesco']                                   );
                    $obRPessoalServidor->roRPessoalDependente->setDependenteInvalido                    ( ($arDependente['boFilhoEquiparado'] == 't') ? true : false          );
                    $obRPessoalServidor->roRPessoalDependente->setCarteiraVacinacao                     ( ($arDependente['boCarteiraVacinacao'] == 't') ? true : false        );
                    $obRPessoalServidor->roRPessoalDependente->setComprovanteMatricula                  ( ($arDependente['boComprovanteMatricula'] == 't') ? true : false     );
                    $obRPessoalServidor->roRPessoalDependente->setDependentePrev                  	( ($arDependente['boDependentePrev'] == 't') ? true : false           );

                    $obRPessoalServidor->roRPessoalDependente->setCodVinculo                            ( $arDependente['inCodDependenteIR']                                  );
                    $obRPessoalServidor->roRPessoalDependente->setDataInicioSalarioFamilia              ( $arDependente['dtInicioSalarioFamilia']                             ); 
                    $obRPessoalServidor->roRPessoalDependente->setDependenteSalarioFamilia              ( ($arDependente['boDependenteSalarioFamilia'] == 't') ? true : false );
                    $obRPessoalServidor->roRPessoalDependente->obRPessoalCID->setCodCid                 ( $arDependente['inCodCIDDependente']                                 );
                   
                    if ($arDependente['boincluirDataNascimentoDespendente']) {
                        $obRPessoalServidor->roRPessoalDependente->obRCGMPessoaFisica->setDataNascimento( $arDependente['stDataNascimentoDependente'] );
                    }
                    
                    $arVacinacoes = $arDependente['VACINACAO'];
                    for ($inCounter=0; $inCounter<count($arVacinacoes); $inCounter++) {
                        $arVacinacao = $arVacinacoes[$inCounter];
                        $obRPessoalServidor->roRPessoalDependente->addRPessoalCarteiraVacinacao();
                        $obRPessoalServidor->roRPessoalDependente->roRPessoalCarteiraVacinacao->setDataApresentacao( $arVacinacao['dtApresentacaoCarteiraVacinacao'] );
                        $obRPessoalServidor->roRPessoalDependente->roRPessoalCarteiraVacinacao->setApresentada     ( $arVacinacao['boApresentadaVacinacao'] );
                    }
                    $arMatriculas = $arDependente['MATRICULA'];
                    for ($inCounter=0; $inCounter<count($arMatriculas); $inCounter++) {
                        $arMatricula = $arMatriculas[$inCounter];
                        $obRPessoalServidor->roRPessoalDependente->addRPessoalComprovanteMatricula();
                        $obRPessoalServidor->roRPessoalDependente->roRPessoalComprovanteMatricula->setDataApresentacao( $arMatricula['dtApresentacaoComprovanteMatricula']);
                        $obRPessoalServidor->roRPessoalDependente->roRPessoalComprovanteMatricula->setApresentada     ( $arMatricula['boApresentadaMatricula']);
                    }
                }
            }

            //monta array de atributos dinamicos
            foreach ($arChave as $key => $value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/" , $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode( "," , $value );
                }
                $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }

            $obErro = $obRPessoalServidor->incluirServidor($boTransacao);

        }

        if (!$obErro->ocorreu()) {
            $obTransacao->commitAndClose();
            sistemaLegado::alertaAviso($pgFilt,"Registro: ".$obRPessoalServidor->roUltimoContratoServidor->getRegistro()." - ".$rsCGM->getCampo("nom_cgm"),"incluir","aviso", Sessao::getId(), "../");
        } else {
            $obTransacao->rollbackAndClose();
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;

    case "alterar":
    case "alterar_servidor":
        $obErro = new Erro;
        if (Sessao::read('FOTO_NAME') and Sessao::read('FOTO_NAME') != "no_foto.jpeg") {
            //NOME DO ARQUIVO DA MINIATURA
            $imagem_gerada = explode(".", Sessao::read('FOTO_NAME'));
            $nome_foto = sistemaLegado::getmicrotime() . "_" . $imagem_gerada[0];
            $imagem_gerada = CAM_GRH_PES_ANEXOS. $nome_foto . "_mini.jpg";
            $nome_foto = $nome_foto ."_mini.jpg";
            $stFoto = fopen($imagem_gerada,"w+");
            fwrite ($stFoto, Sessao::read('FOTO_BIN'));
            fclose($stFoto);
        } elseif (Sessao::read('FOTO_NAME') and Sessao::read('FOTO_NAME') == "no_foto.jpeg") {
            $nome_foto = Sessao::read('FOTO_NAME');
        }
        $obRPessoalServidor->setCodServidor                    ( $_REQUEST['inCodServidor']);
        $obRPessoalServidor->setCodUF                          ( $_POST['inCodUF']);
        $obRPessoalServidor->setCodMunicipio                   ( $_POST['inCodMunicipio'] );
        $obRPessoalServidor->setCodEstadoCivil                 ( $_POST['inCodEstadoCivil']);
        $obRPessoalServidor->setCodRais                        ( $_POST['inCodRaca']);
        $obRPessoalServidor->recuperaTodosRaca( $rsRaca );
        $obRPessoalServidor->setCodRaca                        ( $rsRaca->getCampo('cod_raca') );
        $obRPessoalServidor->obRPessoalCID->setCodCID          ( $_POST['inCodCID']);
        $obRPessoalServidor->setCodEdital                      ( '0' );
        $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM     ( $_POST['inNumCGM'] );
        $obRPessoalServidor->obRCGMPessoaFisica->setCPF        ( $_POST['stCPF'] );
        $obRPessoalServidor->setNomePai                        ( $_POST['stNomePai'] );
        $obRPessoalServidor->setNomeMae                        ( $_POST['stNomeMae'] );
        $obRPessoalServidor->obRCGMPessoaFisicaConjuge->setNumCgm( $_POST['inCGMConjuge'] );
        $obRPessoalServidor->setDataNascimento                 ( $_POST['stDataNascimento'] );
        $obRPessoalServidor->setDataLaudo                 ( $_POST['dtDataLaudo'] );

        //dados aba documentacao
        if (!$obErro->ocorreu() && isset($_POST['stPisPasep']) && !checkPIS($_POST['stPisPasep'], false)) {
            $obErro->setDescricao("Campo PIS/PASEP da guia Documentação é inválido(".$_POST['stPisPasep'].").");
        }
        $obRPessoalServidor->setPisPasep                       ( $_POST['stPisPasep']                  );
        $obRPessoalServidor->setDataPisPasep                   ( $_POST['dtCadastroPis']               );
        $obRPessoalServidor->setCarteiraReservista             ( $_POST['stCertificadoReservista']     );
        $obRPessoalServidor->setCategoriaReservista            ( $_POST['inCategoriaCertificado']      );
        $obRPessoalServidor->setOrigemReservista               ( $_POST['inOrgaoExpedidorCertificado'] );
        $obRPessoalServidor->setNrTituloEleitor                ( $_POST['inTituloEleitor']             );
        $obRPessoalServidor->setZonaTitulo                     ( $_POST['inZonaTitulo']                );
        $obRPessoalServidor->setSecaoTitulo                    ( $_POST['inSecaoTitulo']               );
        $obRPessoalServidor->setCaminhoFoto                    ( $nome_foto                            );

        $arrCTPS = Sessao::read('CTPS');
        if (is_array($arrCTPS) ) {
            foreach ($arrCTPS as $arCTPS) {
                $obRPessoalServidor->addRPessoalCTPS();
                $obRPessoalServidor->roRPessoalCTPS->setCodCTPS         ( $arCTPS['inCodCTPS']              );
                $obRPessoalServidor->roRPessoalCTPS->setNumero          ( $arCTPS['inNumeroCTPS']           );
                $obRPessoalServidor->roRPessoalCTPS->setOrgaoExpedidor  ( $arCTPS['stOrgaoExpedidorCTPS']   );
                $obRPessoalServidor->roRPessoalCTPS->setSerie           ( $arCTPS['stSerieCTPS']            );
                $obRPessoalServidor->roRPessoalCTPS->setEmissao         ( $arCTPS['dtDataCTPS']             );
                $obRPessoalServidor->roRPessoalCTPS->setUfCTPS          ( $arCTPS['inCodUF']                );

            }
        }
        if ( !$obErro->ocorreu() ) {
            // verificando se a data de alteração da função é maior que a data de nomeação
            $dtDataAlteracaoFuncao = ( $_REQUEST["dtDataAlteracaoFuncao"] != "" ) ? $_REQUEST["dtDataAlteracaoFuncao"] : $_REQUEST["hdnDataAlteracaoFuncao"];
            if ($dtDataAlteracaoFuncao == "") {

                $dtPosse    = implode('',array_reverse(explode('/',$_POST['dtDataPosse'])));
                $dtAdmissao = implode('',array_reverse(explode('/',$_POST['dtAdmissao'])));
                $dtNomeacao = implode('',array_reverse(explode('/',$_POST['dtDataNomeacao'])));

                $dtDataAlteracaoFuncao = $_POST['dtDataPosse'];

                if ($dtNomeacao > $dtPosse) {
                    $dtDataAlteracaoFuncao = $_POST['dtDataNomeacao'];
                }
                if ($dtAdmissao > $dtPosse) {
                    $dtDataAlteracaoFuncao = $_POST['dtAdmissao'];
                }
                if ($dtNomeacao > $dtAdmissao) {
                    $dtDataAlteracaoFuncao = $_POST['dtDataNomeacao'];
                }
            }

            if ($_POST['stContagemInicial'] == 'dtNomeacao') {
                if ( compData($dtDataAlteracaoFuncao , $_POST['dtDataNomeacao'] ) == 2 ) {
                    $obErro->setDescricao("A data de Alteração da função deve ser maior ou igual a Data de Nomeação!");
                }
            } else {
                if ( compData( $dtDataAlteracaoFuncao, $_POST['dtDataPosse']) == 2 ) {
                     $obErro->setDescricao("A data de Alteração da função deve ser maior ou igual a Data da Posse!");
                }
            }
        }//

        if ( !$obErro->ocorreu() ) {
            //dados aba contrato
            $obRPessoalServidor->addContratoServidor();
            $obRPessoalServidor->roUltimoContratoServidor->setCodContrato                               ( $_REQUEST["inCodContrato"]          );
            $obRPessoalServidor->roUltimoContratoServidor->setAlteracaoFuncao                           ( $dtDataAlteracaoFuncao            );
            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioBancoSalario->setNumBanco        ( $_POST[inCodBancoSalario]         );
            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaSalario->setCodAgencia    ( $_POST[inCodAgenciaSalario]       );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalVinculoEmpregaticio->setCodVinculoEmpregaticio( $_POST[inCodVinculoEmpregaticio]  );
            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioBancoFGTS->setNumBanco           ( $_POST[inCodBancoFGTS]            );
            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaFGTS->setCodAgencia       ( $_POST[inCodAgenciaFGTS]          );
            $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoSindicato->obRCGM->setNumCGM( $_POST['inNumCGMSindicato']       );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->setCodOcorrencia       ( $_POST[stNumClassificacao]          );
            $obRPessoalServidor->roUltimoContratoServidor->setCodConselho                               ( $_POST['inCodConselho'] );
            $obRPessoalServidor->roUltimoContratoServidor->setNroConselho                               ( $_POST['inNumeroConselho'] );
            $obRPessoalServidor->roUltimoContratoServidor->setValidadeConselho                          ( $_POST['dtDataValidadeConselho'] );
            if ($_POST["inCodCargo"]) {
                $inCodCargoTMP = $_POST['inCodCargo'];
            } else {
                $inCodCargoTMP = $_POST['inHdnCodCargo'];
            }
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($inCodCargoTMP);

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->addEspecialidadeSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $_POST[inCodEspecialidadeCargo]   );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
            if ($_POST[inHdnCodSubDivisao] != "") {
                $inCodSubDivisao = $_POST[inHdnCodSubDivisao];
            } else {
                $inCodSubDivisao = $_POST[inCodSubDivisao];
            }
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $inCodSubDivisao );

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(false);
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargo);

            if ($rsCargo->getNumLinhas() < 1) {
                sistemaLegado::exibeAviso('Cargo Inválido. Norma não está mais em vigor.', 'n_alterar', 'erro');
                exit;
            }

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegimeFuncao->setCodRegime         ( $_POST[inCodRegimeFuncao]         );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->setCodCargo           ( $_POST[inCodFuncao]               );

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($_POST['inCodFuncao']);
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(false);
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsFuncao);

            if ($rsFuncao->getNumLinhas() < 1) {
                sistemaLegado::exibeAviso('Função Inválida. Norma não está mais em vigor.', 'n_alterar', 'erro');
                exit;
            }

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($inCodCargoTMP);

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addEspecialidade();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->addEspecialidadeSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->setCodEspecialidade( $_POST[inCodEspecialidadeFuncao]  );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addCargoSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $_POST[inCodSubDivisaoFuncao]);
            if ($_POST[inHdnCodRegime] != "") {
                $inCodRegime = $_POST[inHdnCodRegime];
            } else {
                $inCodRegime = $_POST[inCodRegime];
            }
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->setCodRegime                 ( $inCodRegime            );

            //Verifica Norma
            $arCodNorma = explode("/",$_POST['stCodNorma']);
            if (count($arCodNorma)>0) {
                $stNumNorma = ltrim($arCodNorma[0],'0');
                if ($stNumNorma == "") {
                    $stNumNorma = "0";
                }
                $obRNorma = new RNorma();
                $obRNorma->setNumNorma( $stNumNorma );
                $obRNorma->setExercicio( $arCodNorma[1] );
                $obRNorma->listar($rsNorma);
                $stCodNorma = $rsNorma->getCampo('cod_norma');
            }

            $obRPessoalServidor->roUltimoContratoServidor->obROrganogramaOrgao->setCodOrgao               ( $_POST["hdnUltimoOrgaoSelecionado"]     );
            $obRPessoalServidor->roUltimoContratoServidor->obROrganogramaLocal->setCodLocal               ( $_POST[inCodLocal]                      );
            $obRPessoalServidor->roUltimoContratoServidor->obRNorma->setCodNorma                          ( $stCodNorma                             );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoAdmissao->setCodTipoAdmissao     ( $_POST[inCodTipoAdmissao]               );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalFormaPagamento->setCodFormaPagamento ( $_POST[inCodFormaPagamento]             );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoPagamento->setCodTipoPagamento   ( $_POST[inCodTipoPagamento]        	    );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoSalario->setCodTipoSalario       ( $_POST[inCodTipoSalario]          	    );
            $obRPessoalServidor->roUltimoContratoServidor->setRegistro                                    ( $_POST[inContrato]                	    );
            $obRPessoalServidor->roUltimoContratoServidor->setNroCartaoPonto                              ( $_POST[inCartaoPonto]             	    );
            $obRPessoalServidor->roUltimoContratoServidor->setAtivo                                       ( ( $_POST['stSituacao'] == 1 ) ? true : false );
            $obRPessoalServidor->roUltimoContratoServidor->setNomeacao                                    ( $_POST[dtDataNomeacao]                  );
            $obRPessoalServidor->roUltimoContratoServidor->setPosse                                       ( $_POST[dtDataPosse]                     );
            $obRPessoalServidor->roUltimoContratoServidor->setAdmissao                                    ( $_POST[dtAdmissao]                      );
            $obRPessoalServidor->roUltimoContratoServidor->setDataBase                                    ( $_POST[dtDataBase]                      );
            $obRPessoalServidor->roUltimoContratoServidor->setValidadeExameMedico                         ( $_POST[dtValidadeExameMedico]           );
            $obRPessoalServidor->roUltimoContratoServidor->setOpcaoFgts                                   ( $_POST[dtDataFGTS]                      );
            $obRPessoalServidor->roUltimoContratoServidor->setContaCorrenteFgts                           ( $_POST[inContaCreditoFGTS]              );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->setCodPadrao( $_POST[inCodPadrao]);
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->addNivelPadrao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setCodNivelPadrao( $_POST[inCodProgressao] );
            $obRPessoalServidor->roUltimoContratoServidor->setSalario                                     ( $_POST[inSalario]                        );
            $obRPessoalServidor->roUltimoContratoServidor->setHrMensal                                    ( $_POST[stHorasMensais]            	     );
            $obRPessoalServidor->roUltimoContratoServidor->setHrSemanal                                   ( $_POST[stHorasSemanais]          	     );
            $obRPessoalServidor->roUltimoContratoServidor->setInicioProgressao                            ( $_POST[dtDataProgressao]          	     );
            $obRPessoalServidor->roUltimoContratoServidor->setContaCorrenteSalario                        ( $_POST[inContaSalario]            	     );
            $obRPessoalServidor->roUltimoContratoServidor->setAdiantamento                                ( $_POST[boAdiantamento]            	     );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCategoria->setCodCategoria           ( $_POST[inCodCategoria]            	     );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->setCodGrade            ( $_POST['inCodGradeHorario']       	     );
            $obRPessoalServidor->roUltimoContratoServidor->setVigenciaSalario                             ( $_POST['dtVigenciaSalario'] 				);

            //dados aba dependente
            $arDependentes = Sessao::read('DEPENDENTE');
            if ( is_array($arDependentes) ) {
                for ($inCount=0; $inCount<count($arDependentes); $inCount++) {
                    $arDependente = $arDependentes[$inCount];
                    $obRPessoalServidor->addRPessoalDependente();
                    $obRPessoalServidor->roRPessoalDependente->setCodDependente             ( $arDependente['inCodDependente']                                    );
                    $obRPessoalServidor->roRPessoalDependente->obRCGMPessoaFisica->setNumCgm( $arDependente['inCGMDependente']                                    );
                    $obRPessoalServidor->roRPessoalDependente->setCodGrau                   ( $arDependente['stGrauParentesco']                                   );
                    $obRPessoalServidor->roRPessoalDependente->setDependenteInvalido        ( ($arDependente['boFilhoEquiparado'] == 't') ? true : false          );
                    $obRPessoalServidor->roRPessoalDependente->setCarteiraVacinacao         ( ($arDependente['boCarteiraVacinacao'] == 't') ? true : false        );
                    $obRPessoalServidor->roRPessoalDependente->setComprovanteMatricula      ( ($arDependente['boComprovanteMatricula'] == 't') ? true : false     );
                    $obRPessoalServidor->roRPessoalDependente->setDependentePrev            ( ($arDependente['boDependentePrev'] == 't') ? true : false           );

                    $obRPessoalServidor->roRPessoalDependente->setCodVinculo                ( $arDependente['inCodDependenteIR']                                  );
                    $obRPessoalServidor->roRPessoalDependente->setDataInicioSalarioFamilia  ( $arDependente['dtInicioSalarioFamilia']                             );
                    $obRPessoalServidor->roRPessoalDependente->setDependenteSalarioFamilia  ( ($arDependente['boDependenteSalarioFamilia'] == 't') ? true : false );
                    $obRPessoalServidor->roRPessoalDependente->obRPessoalCID->setCodCid     ( $arDependente['inCodCIDDependente']                                 );
                    
                    if ($arDependente['boincluirDataNascimentoDespendente']) {
                        $obRPessoalServidor->roRPessoalDependente->obRCGMPessoaFisica->setDataNascimento( $arDependente['stDataNascimentoDependente'] );
                    }
                    
                    $arVacinacoes = $arDependente['VACINACAO'];
                    for ($inCounter=0; $inCounter<count($arVacinacoes); $inCounter++) {
                        $arVacinacao = $arVacinacoes[$inCounter];
                        $obRPessoalServidor->roRPessoalDependente->addRPessoalCarteiraVacinacao();
                        $obRPessoalServidor->roRPessoalDependente->roRPessoalCarteiraVacinacao->setDataApresentacao( $arVacinacao['dtApresentacaoCarteiraVacinacao'] );
                        $obRPessoalServidor->roRPessoalDependente->roRPessoalCarteiraVacinacao->setApresentada     ( $arVacinacao['boApresentadaVacinacao'] );
                    }
                    
                    $arMatriculas = $arDependente['MATRICULA'];
                    for ($inCounter=0; $inCounter<count($arMatriculas); $inCounter++) {
                        $arMatricula = $arMatriculas[$inCounter];
                        $obRPessoalServidor->roRPessoalDependente->addRPessoalComprovanteMatricula();
                        $obRPessoalServidor->roRPessoalDependente->roRPessoalComprovanteMatricula->setDataApresentacao( $arMatricula['dtApresentacaoComprovanteMatricula']);
                        $obRPessoalServidor->roRPessoalDependente->roRPessoalComprovanteMatricula->setApresentada     ( $arMatricula['boApresentadaMatricula']);
                    }
                }
            }
            //monta array de atributos dinamicos
            foreach ($arChave as $key => $value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/" , $key );
                $inCodAtributo = $arChaves[0];

                if ( is_array($value) ) {

                    foreach ($value as $inCodValor) {
                        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $inCodValor );
                    }
                } else {

                    $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                }
            }

            $obErro = $obRPessoalServidor->alterarServidor();
        }
        if ( !$obErro->ocorreu() )
            if ($_POST['actVoltar']) {
                // a variavel actVoltar contém o nome do programa que chamou a tela de servidor,
                // pra onde o sistema deve retornar se ela estiver vazia o sistema retorno para
                // a listagem de servidores
                sistemaLegado::alertaAviso($_POST['actVoltar'] . '?inNumCGM='.$_POST['inNumCGM'].'&inContrato='. $obRPessoalServidor->roUltimoContratoServidor->getRegistro(), "Registro: ".$obRPessoalServidor->roUltimoContratoServidor->getRegistro()." - ".$rsCGM->getCampo("nom_cgm"),"incluir","aviso", Sessao::getId(), "../");

            } else {
                sistemaLegado::alertaAviso($pgList .'&inContrato='. $obRPessoalServidor->roUltimoContratoServidor->getRegistro(), "Registro: ".$obRPessoalServidor->roUltimoContratoServidor->getRegistro()." - ".$rsCGM->getCampo("nom_cgm"),"alterar","aviso", Sessao::getId(), "../");
            } else
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");

    break;

    case "excluir":

        $obRPessoalServidor->setCodServidor                          ( $_REQUEST['inCodServidor'] );
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST['inCodContrato']   );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->addEspecialidadeSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->addRPessoalCTPS();
        $obRPessoalServidor->addRPessoalDependente();
        $obErro = $obRPessoalServidor->excluirServidor($boTransacao);
        if ( !$obErro->ocorreu() )
            sistemaLegado::alertaAviso($pgList,"Servidor: ".$_REQUEST['inNumCGM']." - ".$rsCGM->getCampo("nom_cgm"),"excluir","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::alertaAviso( $pgList."?stAcao=excluir&".$stFiltro, urlencode($obErro->getDescricao()), "n_excluir","erro",Sessao::getId(),"../" );

    break;
}

/*
recebe duas datas retorna 0 se forem iguais 1 se a primeira for maior e 2 se a segunda for maior
*/

function compData($Data1 = '', $Data2 = '')
{
    $Data1 = explode ('/', $Data1);
    $Data1 = ($Data1[2] . $Data1[1] . $Data1[0]) *1 ;

    $Data2 = explode ('/', $Data2);
    $Data2 = ($Data2[2] . $Data2[1] . $Data2[0]) *1 ;

    if ( $Data1 == $Data2 )
        return 0;
    elseif ($Data1 > $Data2 )
        return 1;
    else
        return 2;

}

function checkPIS($pis, $checkZero=true)
{
    $pis = trim(preg_replace("/[^0-9]/", "", $pis));

    if (trim($pis) === "00000000000" && $checkZero==false) {
        return true;
    }

    if (strlen($pis) != 11 || intval($pis) == 0) {
        return false;
    }

    for ($d = 0, $p = 2, $c = 9; $c >= 0; $c--, ($p < 9) ? $p++ : $p = 2) {
        $d += $pis[$c] * $p;
    }

    return ($pis[10] == (((10 * $d) % 11) % 10));
}
?>
