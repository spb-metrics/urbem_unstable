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
* Página de Processamento da Geração de Assentamento
* Data de Criação: 09/08/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30860 $
$Name:  $
$Author: souzadl $
$Date: 2008-01-10 15:25:38 -0200 (Qui, 10 Jan 2008) $

* Casos de uso: uc-04.04.14
*/

/*
$Log: PRManterGeracaoAssentamento.php,v $
Revision 1.10  2007/10/17 13:36:52  souzadl
construção do caso de uso 04.04.18

Revision 1.9  2007/03/23 18:06:10  souzadl
Bug #8875#

Revision 1.8  2007/01/08 11:00:38  souzadl
inclusão do processo de exclusão e consulta

Revision 1.7  2006/08/25 11:08:16  souzadl
alterado case alterar

Revision 1.6  2006/08/08 17:46:21  vandre
Adicionada tag log.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamentoGeradoContratoServidor.class.php"               );

$arLink  = Sessao::read('link');
$stAcao  = $request->get('stAcao');
$stLink  = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];

$stLink .= '&inCodLotacao='      .$request->get('inCodLotacao');
$stLink .= '&inCodAssentamento=' .$request->get('inCodAssentamento');
$stLink .= '&inContrato='        .$request->get('inContrato');
$stLink .= '&boCargoExercido='   .$request->get('boCargoExercido');
$stLink .= '&inCodCargo='        .$request->get('inCodCargo');
$stLink .= '&inCodEspecialidade='.$request->get('inCodEspecialidade');
$stLink .= '&boFuncaoExercida='  .$request->get('boFuncaoExercida');
$stLink .= '&stDataInicial='     .$request->get('stDataInicial');
$stLink .= '&stDataFinal='       .$request->get('stDataFinal');
$stLink .= '&stModoGeracao='     .$request->get('stModoGeracao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterGeracaoAssentamento";
$pgForm     = "FM".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgFilt     = "FL".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgProc     = "PR".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgOcul     = "OC".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgList     = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgJS       = "JS".$stPrograma.".js";

$obErro = new Erro();
$obTransacao = new Transacao;
$boFlagTransacao = false;
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );        

$obRPessoalAssentametoGeradoContratoServidor = new RPessoalAssentamentoGeradoContratoServidor;
$obRPessoalAssentametoGeradoContratoServidor->addRPessoalGeracaoAssentamento();

switch ($stAcao) {
    case "incluir":
        $arAssentamentos = Sessao::read('arAssentamentos');
        $arContratos = array();
        $stModoGeracao = ( $_POST['stModoGeracao'] ) ? $_POST['stModoGeracao'] : $_POST['hdnModoGeracao'];
        
        switch ($stModoGeracao) {
            case "contrato";
            case "cgm/contrato";
                foreach ($arAssentamentos as $arAssentamento) {
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->setRegistro($arAssentamento['inRegistro']);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->consultarContrato($boTransacao);
                    $arTemp['cod_contrato']             = $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->getCodContrato();
                    $arTemp['cod_assentamento']         = $arAssentamento['inCodAssentamento'];
                    $arTemp['periodo_inicial']          = $arAssentamento['stDataInicial'];
                    $arTemp['periodo_final']            = $arAssentamento['stDataFinal'];
                    $arTemp['dt_inicial']               = $arAssentamento['dtInicial'];
                    $arTemp['dt_final']                 = $arAssentamento['dtFinal'];
                    $arTemp['observacao']               = $arAssentamento['stObservacao'];
                    $arTemp['arNormas']                 = $arAssentamento['arNormas'];
                    $arTemp['inCodClassificacao']       = $arAssentamento['inCodClassificacao'];
                    $arTemp['inCodNorma']               = $arAssentamento['inCodNorma'];
                    $arTemp['inCodTipoNorma']           = $arAssentamento['inCodTipoNorma'];
                    $arTemp['hdnDataAlteracaoFuncao']   = $arAssentamento['hdnDataAlteracaoFuncao'];
                    $arTemp['inCodProgressao']          = $arAssentamento['inCodProgressao'];
                    $arTemp['inCodRegime']              = $arAssentamento['inCodRegime'];
                    $arTemp['inCodSubDivisao']          = $arAssentamento['inCodSubDivisao'];
                    $arTemp['stSubDivisao']             = $arAssentamento['stSubDivisao'];
                    $arTemp['stCargo']                  = $arAssentamento['stCargo'];
                    $arTemp['inCodEspecialidadeCargo']  = $arAssentamento['inCodEspecialidadeCargo'];
                    $arTemp['stEspecialidadeCargo']     = $arAssentamento['stEspecialidadeCargo'];
                    $arTemp['inCodRegimeFuncao']        = $arAssentamento['inCodRegimeFuncao'];
                    $arTemp['stRegimeFuncao']           = $arAssentamento['stRegimeFuncao'];
                    $arTemp['inCodSubDivisaoFuncao']    = $arAssentamento['inCodSubDivisaoFuncao'];
                    $arTemp['stSubDivisaoFuncao']       = $arAssentamento['stSubDivisaoFuncao'];
                    $arTemp['inCodFuncao']              = $arAssentamento['inCodFuncao'];
                    $arTemp['stFuncao']                 = $arAssentamento['stFuncao'];
                    $arTemp['inCodEspecialidadeFuncao'] = $arAssentamento['inCodEspecialidadeFuncao'];
                    $arTemp['stEspecialidadeFuncao']    = $arAssentamento['stEspecialidadeFuncao'];
                    $arTemp['dtDataAlteracaoFuncao']    = $arAssentamento['dtDataAlteracaoFuncao'];
                    $arTemp['stHorasMensais']           = $arAssentamento['stHorasMensais'];
                    $arTemp['stHorasSemanais']          = $arAssentamento['stHorasSemanais'];
                    $arTemp['inCodPadrao']              = $arAssentamento['inCodPadrao'];
                    $arTemp['stPadrao']                 = $arAssentamento['stPadrao'];
                    $arTemp['inSalario']                = $arAssentamento['inSalario'];
                    $arTemp['dtVigenciaSalario']        = $arAssentamento['dtVigenciaSalario'];

                    $arContratos[] = $arTemp;
                }
            break;
            case "cargo";
                $arContratosSearch = array();
                foreach ($arAssentamentos as $arAssentamento) {
                    $rsContratos = new RecordSet;
                    if ($arAssentamento['boCargoExercido']) {
                        if ($arAssentamento['inCodEspecialidade']) {
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->addEspecialidade();
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade($arAssentamento['inCodEspecialidade']);
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->listarContratosCargoExercidoComSubDivisaoAssentamento($rsContratos, $arAssentamento['inCodAssentamento'],$boTransacao);
                        } else {
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->setCodCargo($arAssentamento['inCodCargo']);
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->addEspecialidade();
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade("");
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->listarContratosCargoExercidoComSubDivisaoAssentamento($rsContratos, $arAssentamento['inCodAssentamento'],$boTransacao);
                        }
                    }
                    while ( !$rsContratos->eof() ) {
                        if ( array_search($rsContratos->getCampo('cod_contrato'),$arContratosSearch) === false ) {
                            $arTemp['cod_contrato']       = $rsContratos->getCampo('cod_contrato');
                            $arTemp['cod_assentamento']   = $arAssentamento['inCodAssentamento'];
                            $arTemp['inCodClassificacao'] = $arAssentamento['inCodClassificacao'];
                            $arTemp['periodo_inicial']    = $arAssentamento['stDataInicial'];
                            $arTemp['periodo_final']      = $arAssentamento['stDataFinal'];
                            $arTemp['dt_inicial']         = $arAssentamento['dtInicial'];
                            $arTemp['dt_final']           = $arAssentamento['dtFinal'];
                            $arTemp['observacao']         = $arAssentamento['stObservacao'];
                            $arTemp['arNormas']           = $arAssentamento['arNormas'];
                            $arContratos[] = $arTemp;
                        }
                        $arContratosSearch[] = $rsContratos->getCampo('cod_contrato');
                        $rsContratos->proximo();
                    }

                    if ($arAssentamento['boFuncaoExercida']) {
                        if ($arAssentamento['inCodEspecialidade']) {
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->addEspecialidade();
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade($arAssentamento['inCodEspecialidade']);
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->listarContratosFuncaoExercidaComSubDivisaoAssentamento($rsContratos,$arAssentamento['inCodAssentamento'],$boTransacao);
                        } else {
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->addEspecialidade();
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->setCodCargo($arAssentamento['inCodCargo']);
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade("");
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->listarContratosFuncaoExercidaComSubDivisaoAssentamento($rsContratos, $arAssentamento['inCodAssentamento'],$boTransacao);
                        }
                    }

                    while ( !$rsContratos->eof() ) {
                        if ( array_search($rsContratos->getCampo('cod_contrato'),$arContratosSearch) === false ) {
                            $arTemp['cod_contrato']       = $rsContratos->getCampo('cod_contrato');
                            $arTemp['cod_assentamento']   = $arAssentamento['inCodAssentamento'];
                            $arTemp['inCodClassificacao'] = $arAssentamento['inCodClassificacao'];
                            $arTemp['periodo_inicial']    = $arAssentamento['stDataInicial'];
                            $arTemp['periodo_final']      = $arAssentamento['stDataFinal'];
                            $arTemp['dt_inicial']         = $arAssentamento['dtInicial'];
                            $arTemp['dt_final']           = $arAssentamento['dtFinal'];
                            $arTemp['observacao']         = $arAssentamento['stObservacao'];
                            $arTemp['arNormas']           = $arAssentamento['arNormas'];
                            $arContratos[] = $arTemp;
                        }
                        $arContratosSearch[] = $rsContratos->getCampo('cod_contrato');
                        $rsContratos->proximo();
                    }
                }
            break;
            case "lotacao";
                foreach ($arAssentamentos as $arAssentamento) {
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obROrganogramaOrgao->setCodOrgaoEstruturado($arAssentamento['inCodLotacao']);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obROrganogramaOrgao->listarOrgaoReduzido($rsOrgranogramaOrgao,'','',$boTransacao);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obROrganogramaOrgao->setCodOrgao($rsOrgranogramaOrgao->getCampo('cod_orgao'));
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->listarContratoServidorLotacaoComSubDivisaoAssentamento($rsContrato, $arAssentamento['inCodAssentamento'],$boTransacao);

                    while ( !$rsContrato->eof() ) {
                        $arTemp['cod_contrato']       = $rsContrato->getCampo('cod_contrato');
                        $arTemp['cod_assentamento']   = $arAssentamento['inCodAssentamento'];
                        $arTemp['inCodClassificacao'] = $arAssentamento['inCodClassificacao'];
                        $arTemp['periodo_inicial']    = $arAssentamento['stDataInicial'];
                        $arTemp['periodo_final']      = $arAssentamento['stDataFinal'];
                        $arTemp['dt_inicial']         = $arAssentamento['dtInicial'];
                        $arTemp['dt_final']           = $arAssentamento['dtFinal'];
                        $arTemp['observacao']         = $arAssentamento['stObservacao'];
                        $arTemp['arNormas']           = $arAssentamento['arNormas'];
                        $arContratos[] = $arTemp;

                        $rsContrato->proximo();
                    }
                    unset($rsContrato);
                }
            break;
        }

        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php");
        $obRPessoalServidor = new RPessoalServidor;
        $obRPessoalServidor->addContratoServidor();

        if ( !$obErro->ocorreu() ) {
            foreach ($arContratos as $keyArContrato => $arContrato) {
                $inCodMotivo = SistemaLegado::pegaDado("cod_motivo","pessoal.assentamento_assentamento"
                                                            ,"WHERE cod_assentamento = ".$arContrato['cod_assentamento']." 
                                                            AND cod_classificacao = ".$arContrato["inCodClassificacao"].""
                                                            ,$boTransacao);

                //Verifica se o cod_motivo é '18 - Readaptação' ou '14 - Alteração de Cargo'
                if ( ($inCodMotivo == 18) || ($inCodMotivo == 14) ){
                    
                    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato              ( $arContrato["cod_contrato"]           );
                    $obRPessoalServidor->roUltimoContratoServidor->setAlteracaoFuncao          ( $arContrato["dtDataAlteracaoFuncao"]  );
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $arContrato["stCargo"]                );
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->addEspecialidadeSubDivisao();
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $arContrato["inCodEspecialidadeCargo"]   );
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $arContrato["inCodSubDivisao"] );
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(false);
                    $obErro = $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargo,$boTransacao);
                    if ( !$obErro->ocorreu() ) {
                        if ($rsCargo->getNumLinhas() < 1) {
                            sistemaLegado::exibeAviso('Cargo Inválido. Norma não está mais em vigor.', 'n_alterar', 'erro');
                            exit;
                        }
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegimeFuncao->setCodRegime( $arContrato["inCodRegimeFuncao"] );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->setCodCargo  ( $arContrato["inCodFuncao"]       );
                
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $arContrato["inCodFuncao"]);
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(false);
                        $obErro = $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsFuncao,$boTransacao);
                        
                        if ( !$obErro->ocorreu() ) {
                            if ($rsFuncao->getNumLinhas() < 1) {
                                sistemaLegado::exibeAviso('Função Inválida. Norma não está mais em vigor.', 'n_alterar', 'erro');
                                exit;
                            }

    
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $arContrato["stCargo"] );
    
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addEspecialidade();
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->addEspecialidadeSubDivisao();
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->setCodEspecialidade( $arContrato["inCodEspecialidadeFuncao"]  );
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addCargoSubDivisao();
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $arContrato["inCodSubDivisaoFuncao"]);
                
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->setCodRegime( $arContrato["inCodRegime"] );
                
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->setCodPadrao( $arContrato["inCodPadrao"]);
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->addNivelPadrao();
                
                            $obRPessoalServidor->roUltimoContratoServidor->setSalario                                     ( $arContrato["inSalario"] );
                            $obRPessoalServidor->roUltimoContratoServidor->setHrMensal                                    ( $arContrato["stHorasMensais"]                   );
                            $obRPessoalServidor->roUltimoContratoServidor->setHrSemanal                                   ( $arContrato["stHorasSemanais"]                  );
                            $obRPessoalServidor->roUltimoContratoServidor->setVigenciaSalario                             ( $arContrato["dtVigenciaSalario"]                );
                    
                            $obErro = $obRPessoalServidor->roUltimoContratoServidor->listarDadosAbaContratoServidor($rsCargoServidor,$boTransacao);
                    
                            if ( !$obErro->ocorreu() ) {

                                while ( !$rsCargoServidor->eof() ) {
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoPagamento->setCodTipoPagamento             ( $rsCargoServidor->getCampo('cod_tipo_pagamento') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoSalario->setCodTipoSalario                 ( $rsCargoServidor->getCampo('cod_tipo_salario') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoAdmissao->setCodTipoAdmissao               ( $rsCargoServidor->getCampo('cod_tipo_admissao') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalVinculoEmpregaticio->setCodVinculoEmpregaticio ( $rsCargoServidor->getCampo('cod_vinculo') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCategoria->setCodCategoria                     ( $rsCargoServidor->getCampo('cod_categoria') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->setCodGrade                      ( $rsCargoServidor->getCampo('cod_grade') );
                                    $obRPessoalServidor->roUltimoContratoServidor->setNomeacao                                              ( $rsCargoServidor->getCampo('dt_nomeacao') );
                                    $obRPessoalServidor->roUltimoContratoServidor->setPosse                                                 ( $rsCargoServidor->getCampo('dt_posse') );
                                    $obRPessoalServidor->roUltimoContratoServidor->setAdmissao                                              ( $rsCargoServidor->getCampo('dt_admissao') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoSindicato->obRCGM->setNumCGM            ( $rsCargoServidor->getCampo('numcgm_sindicato') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalFormaPagamento->setCodFormaPagamento           ( $rsCargoServidor->getCampo('cod_forma_pagamento') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRNorma->setCodNorma                                    ( $rsCargoServidor->getCampo('cod_norma') );
    
                                    $rsCargoServidor->proximo();
                                }

                                $obErro = $obRPessoalServidor->roUltimoContratoServidor->alterarContrato($boTransacao);
                            }
                        }
                    }                
                }

                $obRPessoalAssentametoGeradoContratoServidor = new RPessoalAssentamentoGeradoContratoServidor;
                $obRPessoalAssentametoGeradoContratoServidor->addRPessoalGeracaoAssentamento();

                if ( !$obErro->ocorreu() ) {
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->setCodContrato ( $arContrato['cod_contrato']);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalAssentamento->setCodAssentamento ( $arContrato['cod_assentamento']);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setDescricaoObservacao                     ( $arContrato['observacao']);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoInicial                          ( $arContrato['periodo_inicial']);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoFinal                            ( $arContrato['periodo_final']);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoLicPremioInicial                 ( $arContrato['dt_inicial']);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoLicPremioFinal                   ( $arContrato['dt_final']);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setCodNorma                                ( $arContrato['arNormas']);
                    $obRPessoalServidor->roUltimoContratoServidor->obRNorma->setCodNorma                                                    ( $arContrato['arNormas']);
                    $obErro = $obRPessoalAssentametoGeradoContratoServidor->incluirAssentamentoGeradoContratoServidor($boTransacao);
                    unset($arContratos[$keyArContrato]);
                }
                
                if( $obErro->ocorreu() ) 
                    break;
            }
        }
                
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRPessoalAssentametoGeradoContratoServidor->obTPessoalConselho );

        if ( !$obErro->ocorreu() ) {
            Sessao::write('arAssentamentos', array());
            sistemaLegado::alertaAviso($pgForm,"Gerar Assentamento","incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obRPessoalServidor = new RPessoalServidor;
        $obRPessoalServidor->addContratoServidor();
        $arNormas = Sessao::read('arNormas');

        $inCodMotivo = SistemaLegado::pegaDado("cod_motivo","pessoal.assentamento_assentamento"
                                                            ,"WHERE cod_assentamento = ".$_REQUEST['inCodAssentamento']." 
                                                            AND cod_classificacao = ".$_REQUEST['inCodClassificacao']."");


        //Verifica se o cod_motivo é '18 - Readaptação' ou '14 - Alteração de Cargo'
        if ( ($inCodMotivo == 18) || ($inCodMotivo == 14) ){

            $inRegistro = $_POST["inRegistro"];
            $inCodContrato = SistemaLegado::pegaDado("cod_contrato","pessoal.contrato","WHERE registro = ".$inRegistro);
            $obRPessoalServidor->roUltimoContratoServidor->setCodContrato                               ( $inCodContrato            );
            $obRPessoalServidor->roUltimoContratoServidor->setAlteracaoFuncao                           ( $_REQUEST["dtDataAlteracaoFuncao"] );
        
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($_POST['stCargo']);

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->addEspecialidadeSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $_POST["inCodEspecialidadeCargo"]   );
        
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $_POST["inCodSubDivisao"] );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(false);
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargo);

            if ($rsCargo->getNumLinhas() < 1) {
                sistemaLegado::exibeAviso('Cargo Inválido. Norma não está mais em vigor.', 'n_alterar', 'erro');
                exit;
            }

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegimeFuncao->setCodRegime         ( $_POST["inCodRegimeFuncao"]         );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->setCodCargo           ( $_POST["inCodFuncao"]               );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($_POST['inCodFuncao']);
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(false);
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsFuncao);

            if ($rsFuncao->getNumLinhas() < 1) {
                sistemaLegado::exibeAviso('Função Inválida. Norma não está mais em vigor.', 'n_alterar', 'erro');
                exit;
            }

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($_POST['stCargo']);

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addEspecialidade();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->addEspecialidadeSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->setCodEspecialidade( $_POST["inCodEspecialidadeFuncao"]  );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addCargoSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $_POST["inCodSubDivisaoFuncao"]);
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->setCodRegime( $_POST["inCodRegime"]);

            if ($arNormas != "") {
                foreach ($arNormas as $arNorma) {
                    $arCodNorma[] = $arNorma['inCodNorma'];
                }
            }
    
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->setCodPadrao( $_POST["inCodPadrao"]);
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->addNivelPadrao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setCodNivelPadrao( $_POST["inCodProgressao"] );
            $obRPessoalServidor->roUltimoContratoServidor->setSalario                                     ( $_POST["inSalario"]         );
            $obRPessoalServidor->roUltimoContratoServidor->setHrMensal                                    ( $_POST["stHorasMensais"]    );
            $obRPessoalServidor->roUltimoContratoServidor->setHrSemanal                                   ( $_POST["stHorasSemanais"]   );
            $obRPessoalServidor->roUltimoContratoServidor->setInicioProgressao                            ( $_POST["dtDataProgressao"]  );
            $obRPessoalServidor->roUltimoContratoServidor->setContaCorrenteSalario                        ( $_POST["inContaSalario"]    );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->setCodGrade            ( $_POST["inCodGradeHorario"] );
            $obRPessoalServidor->roUltimoContratoServidor->setVigenciaSalario                             ( $_POST["dtVigenciaSalario"] );
        
            $obRPessoalServidor->roUltimoContratoServidor->listarDadosAbaContratoServidor($rsCargoServidor);

            while ( !$rsCargoServidor->eof() ) {
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoPagamento->setCodTipoPagamento ( $rsCargoServidor->getCampo('cod_tipo_pagamento') );
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoSalario->setCodTipoSalario ( $rsCargoServidor->getCampo('cod_tipo_salario') );
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoAdmissao->setCodTipoAdmissao ( $rsCargoServidor->getCampo('cod_tipo_admissao') );
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalVinculoEmpregaticio->setCodVinculoEmpregaticio ( $rsCargoServidor->getCampo('cod_vinculo') );
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCategoria->setCodCategoria ( $rsCargoServidor->getCampo('cod_categoria') );
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->setCodGrade ( $rsCargoServidor->getCampo('cod_grade') );
                $obRPessoalServidor->roUltimoContratoServidor->setNomeacao ( $rsCargoServidor->getCampo('dt_nomeacao') );
                $obRPessoalServidor->roUltimoContratoServidor->setPosse ( $rsCargoServidor->getCampo('dt_posse') );
                $obRPessoalServidor->roUltimoContratoServidor->setAdmissao ( $rsCargoServidor->getCampo('dt_admissao') );
                $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoSindicato->obRCGM->setNumCGM ( $rsCargoServidor->getCampo('numcgm_sindicato') );
                $obRPessoalServidor->roUltimoContratoServidor->obRNorma->setCodNorma( $rsCargoServidor->getCampo('cod_norma') );
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalFormaPagamento->setCodFormaPagamento( $rsCargoServidor->getCampo('cod_forma_pagamento') );
        
                $rsCargoServidor->proximo();
            }
        
            $obErro = $obRPessoalServidor->roUltimoContratoServidor->alterarContrato($boTransacao);
        }

        if ( !$obErro->ocorreu() ) {
            $obTPessoalContrato = new TPessoalContrato;
            $stFiltro = " WHERE registro = ".$_POST['inRegistro'];
            $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
            $obRPessoalAssentametoGeradoContratoServidor->addRPessoalGeracaoAssentamento();
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setCodAssentamentoGerado( $_POST['inCodAssentamentoGerado'] );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalAssentamento->setCodAssentamento( $_POST['inCodAssentamento'] );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setDescricaoObservacao( $_POST['stObservacao'] );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoInicial( $_POST['stDataInicial'] );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoFinal( $_POST['stDataFinal'] );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->setCodContrato($rsContrato->getCampo("cod_contrato"));
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoLicPremioInicial($_POST['dtInicial']);
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoLicPremioFinal($_POST['dtFinal']);
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setCodNorma($arCodNorma);
            
            $obErro = $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->alterarGeracaoAssentamento();
        }

        if ( !$obErro->ocorreu() ) {
            Sessao::remove('asNormas');
            sistemaLegado::alertaAviso($pgList,"Gerar Assentamento","alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir":
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " WHERE registro = ".$_POST['inRegistro'];
        $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
        $obRPessoalAssentametoGeradoContratoServidor->addRPessoalGeracaoAssentamento();
        $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->setCodContrato($rsContrato->getCampo("cod_contrato"));
        $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalAssentamento->setCodAssentamento( $_POST['inCodAssentamento'] );
        $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setCodAssentamentoGerado( $_POST['inCodAssentamentoGerado'] );
        $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setTimestamp( $_POST['stTimestamp'] );
        $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setDescricaoExclusao( $_POST['stMotivoExclusao'] );
        $obErro = $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->excluirGeracaoAssentamento();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Gerar Assentamento","excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;
}
?>
