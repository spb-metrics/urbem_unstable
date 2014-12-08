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
  * Página de processamento para Configurar IDE
  * Data de criação : 07/01/2014
  * 
  * @author Analista:    Eduardo Paculski Schitz
  * @author Programador: Franver Sarmento de Moraes
  * 
  * @ignore
  * 
  * $Id: PRManterRegistroPreco.php 59612 2014-09-02 12:00:51Z gelson $
  * $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $
  * $Author: gelson $
  * $Rev: 59612 $
  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGProcessoAdesaoRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGLoteRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGItemRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGEmpenhoRegistroPrecos.class.php";

// Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroPreco";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTTCEMGProcessoAdesaoRegistroPrecos = new TTCEMGProcessoAdesaoRegistroPrecos();

Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento( $obTTCEMGProcessoAdesaoRegistroPrecos );

if (empty($stAcao)) {
    $stAcao = 'incluir';
}

$obErro = new Erro;

switch ($stAcao) {

    case 'excluir' :
        $obTTCEMGItemRegistroPrecos = new TTCEMGItemRegistroPrecos();
        $obTTCEMGItemRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
        $obTTCEMGItemRegistroPrecos->setDado('numero_processo_adesao' , $request->get('inNroProcessoAdesao'));
        $obTTCEMGItemRegistroPrecos->setDado('exercicio_adesao'       , $request->get('stExercicioProcessoAdesao'));
        $obErro = $obTTCEMGItemRegistroPrecos->exclusao();

        $obTTCEMGLoteRegistroPrecos = new TTCEMGLoteRegistroPrecos();
        $obTTCEMGLoteRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
        $obTTCEMGLoteRegistroPrecos->setDado('numero_processo_adesao' , $request->get('inNroProcessoAdesao'));
        $obTTCEMGLoteRegistroPrecos->setDado('exercicio_adesao'       , $request->get('stExercicioProcessoAdesao'));
        $obErro = $obTTCEMGLoteRegistroPrecos->exclusao();

        $obTTCEMGProcessoAdesaoRegistroPrecos = new TTCEMGProcessoAdesaoRegistroPrecos();
        $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
        $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('numero_processo_adesao' , $request->get('inNroProcessoAdesao'));
        $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('exercicio_adesao'       , $request->get('stExercicioProcessoAdesao'));
        $obErro = $obTTCEMGProcessoAdesaoRegistroPrecos->exclusao();

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&inCodEntidade=".$request->get('inCodEntidade')."&stAcao=".$stAcao,'Registro de Preço - '.$request->get('inNroProcessoAdesao')."/".$request->get('stExercicioProcessoAdesao'),"excluir","excluir", Sessao::getId(), "../");
    break;

    default:

        $rsProcessosAdesao = new RecordSet();
        $arStCodigoProcesso = explode('/',$request->get('stCodigoProcesso'));
        $arStUnidadeOrcamentaria = explode('.',$request->get('stUnidadeOrcamentaria'));
               
        $arItens = Sessao::read('arItens');
        $arEmpenhos = Sessao::read('arEmpenhos');
        if (!$obErro->ocorreu()) {

            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('numero_processo_adesao' , $arStCodigoProcesso[0]);
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('exercicio_adesao'       , $arStCodigoProcesso[1]);
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->recuperaPorChave( $rsProcessosAdesao, $boTransacao );

            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('data_abertura_processo_adesao'    , $request->get('dtAberturaProcesso'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('numcgm'                           , $request->get('inNumOrgaoGerenciador'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('exercicio_licitacao'              , $request->get('stExercicioProcessoLicitacao'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('numero_processo_licitacao'        , $request->get('stNroProcessoLicitacao'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('codigo_modalidade_licitacao'      , $request->get('inCodModalidadeLicitacao'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('numero_modalidade'                , $request->get('stNroModalidade'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('data_ata_registro_preco'          , $request->get('dtAtaRegistroPreco'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('data_ata_registro_preco_validade' , $request->get('dtValidadeAtaRegistroPreco'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('natureza_procedimento'            , $request->get('inNaturezaProcedimento'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('data_publicacao_aviso_intencao'   , $request->get('dtPublicacaoAvisoIntencao'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('objeto_adesao'                    , $request->get('txtAreaObjetoAdesao'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('cgm_responsavel'                  , $request->get('inNumCGMResponsavel'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('desconto_tabela'                  , $request->get('inDescontoTabela'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('processo_lote'                    , $request->get('inProcessoPorLote'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('exercicio'                        , $request->get('stExercicio'));
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('num_orgao'                        , $arStUnidadeOrcamentaria[0]);
            $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('num_unidade'                      , $arStUnidadeOrcamentaria[1]);

            if ($rsProcessosAdesao->getNumLinhas() < 0) {
                $obErro = $obTTCEMGProcessoAdesaoRegistroPrecos->inclusao($boTransacao);
            } else {
                $obErro = $obTTCEMGProcessoAdesaoRegistroPrecos->alteracao($boTransacao);
            }
       

            # Exclui sempre e inclui se necessário, lote e item.
            if (!$obErro->ocorreu()) {
                
                $obTTCEMGItemRegistroPrecos = new TTCEMGItemRegistroPrecos();
                $obTTCEMGItemRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
                $obTTCEMGItemRegistroPrecos->setDado('numero_processo_adesao' , (int)$arStCodigoProcesso[0]);
                $obTTCEMGItemRegistroPrecos->setDado('exercicio_adesao'       , $arStCodigoProcesso[1]);
                $obErro = $obTTCEMGItemRegistroPrecos->exclusao();
                
                $obTTCEMGEmpenhoRegistroPrecos = new TTCEMGEmpenhoRegistroPrecos();
                $obTTCEMGEmpenhoRegistroPrecos->setDado('cod_entidade', $request->get('inCodEntidade'));
                $obTTCEMGEmpenhoRegistroPrecos->setDado('numero_processo_adesao',(int)$arStCodigoProcesso[0]);
                $obTTCEMGEmpenhoRegistroPrecos->setDado('exercicio_adesao',$arStCodigoProcesso[1]);
                $obErro = $obTTCEMGEmpenhoRegistroPrecos->exclusao();

                $obTTCEMGLoteRegistroPrecos = new TTCEMGLoteRegistroPrecos();
                $obTTCEMGLoteRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
                $obTTCEMGLoteRegistroPrecos->setDado('numero_processo_adesao' , (int)$arStCodigoProcesso[0]);
                $obTTCEMGLoteRegistroPrecos->setDado('exercicio_adesao'       , $arStCodigoProcesso[1]);
                $obErro = $obTTCEMGLoteRegistroPrecos->exclusao();
                
                if (is_array($arItens) && count($arItens) > 0) {

                    $boProcessoPorLote = $request->get('inProcessoPorLote');
                    $inDescontoTabela  = $request->get('inDescontoTabela');
                    
                    $i = 1;

                    foreach ($arItens as $item) {

                        # Cadastro de Lote quando necessário
                        $inCodLote = ((!empty($item['stCodigoLote']) && $item['stCodigoLote'] != 0) ? $item['stCodigoLote'] : 0);
                        $txtDescricaoLote = (!empty($item['txtDescricaoLote']) ? $item['txtDescricaoLote'] : '');

                        $obTTCEMGLoteRegistroPrecos = new TTCEMGLoteRegistroPrecos();
                        $obTTCEMGLoteRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
                        $obTTCEMGLoteRegistroPrecos->setDado('numero_processo_adesao' , (int)$arStCodigoProcesso[0]);
                        $obTTCEMGLoteRegistroPrecos->setDado('exercicio_adesao'       , $arStCodigoProcesso[1]);
                        $obTTCEMGLoteRegistroPrecos->setDado('cod_lote'               , $inCodLote);
                        $obTTCEMGLoteRegistroPrecos->recuperaPorChave( $rsLote );

                        $obTTCEMGLoteRegistroPrecos->setDado('descricao_lote' , $txtDescricaoLote);

                        $nuPercentualLote = ($boProcessoPorLote == true) ? $item['nuPercentualLote'] : 0;

                        $obTTCEMGLoteRegistroPrecos->setDado('percentual_desconto_lote' , $nuPercentualLote);
    
                        if ($rsLote->getNumLinhas() > 0) {
                            $obErro = $obTTCEMGLoteRegistroPrecos->alteracao();
                        } else {
                            $obErro = $obTTCEMGLoteRegistroPrecos->inclusao();
                        }

                        if ($inDescontoTabela == 2 || ($inDescontoTabela == 1 && $boProcessoPorLote == 1)) {
                            $nuPercentualItem = 0;
                        } else {
                            $nuPercentualItem = $item['nuPercentualItem'];
                        }
                        
                        # Cadastro dos Itens do Registro de Preço, vinculação ao lote
                        $obTTCEMGItemRegistroPrecos = new TTCEMGItemRegistroPrecos();
                        $obTTCEMGItemRegistroPrecos->setDado('cod_entidade'              , $request->get('inCodEntidade'));
                        $obTTCEMGItemRegistroPrecos->setDado('numero_processo_adesao'    , (int)$arStCodigoProcesso[0]);
                        $obTTCEMGItemRegistroPrecos->setDado('exercicio_adesao'          , $arStCodigoProcesso[1]);
                        $obTTCEMGItemRegistroPrecos->setDado('cod_lote'                  , $inCodLote);
                        $obTTCEMGItemRegistroPrecos->setDado('cod_item'                  , $item['inCodItem']);
                        $obTTCEMGItemRegistroPrecos->setDado('num_item'                  , (!empty($item['stCodigoLote']) ? $item['stCodigoLote'] : $i));
                        $obTTCEMGItemRegistroPrecos->setDado('data_cotacao'              , $item['dtCotacao']);
                        $obTTCEMGItemRegistroPrecos->setDado('vl_cotacao_preco_unitario' , $item['nuVlReferencia']);
                        $obTTCEMGItemRegistroPrecos->setDado('quantidade_cotacao'        , $item['nuQuantidade']);
                        $obTTCEMGItemRegistroPrecos->setDado('preco_unitario'            , $item['nuVlUnitario']);
                        $obTTCEMGItemRegistroPrecos->setDado('quantidade_licitada'       , $item['nuQtdeLicitada']);
                        $obTTCEMGItemRegistroPrecos->setDado('quantidade_aderida'        , $item['nuQtdeAderida']);
                        $obTTCEMGItemRegistroPrecos->setDado('percentual_desconto'       , $nuPercentualItem);
                        $obTTCEMGItemRegistroPrecos->setDado('cgm_vencedor'              , $item['inNumCGMVencedor']);
                        $obTTCEMGItemRegistroPrecos->inclusao();

                        $i++;
                    }
                }
                if (is_array($arEmpenhos) && count($arEmpenhos) > 0) {
                    foreach ($arEmpenhos as $empenho) {

                        # Cadastro dos Empenhos do Registro de Preço
                        $obTTCEMGEmpenhoRegistroPrecos = new TTCEMGEmpenhoRegistroPrecos();
                        $obTTCEMGEmpenhoRegistroPrecos->setDado('cod_entidade'          , $request->get('inCodEntidade'));
                        $obTTCEMGEmpenhoRegistroPrecos->setDado('numero_processo_adesao', (int)$arStCodigoProcesso[0]);
                        $obTTCEMGEmpenhoRegistroPrecos->setDado('exercicio_adesao'      , $arStCodigoProcesso[1]);
                        $obTTCEMGEmpenhoRegistroPrecos->setDado('cod_empenho'           , $empenho['cod_empenho']);
                        $obTTCEMGEmpenhoRegistroPrecos->setDado('exercicio_empenho'     , $empenho['exercicio']);
                        $obTTCEMGEmpenhoRegistroPrecos->inclusao();
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                # Limpa o array de empenhos que está na sessão.
                Sessao::remove('arEmpenhos');
                Sessao::remove('arItens');
                SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=alterar","Adesão a Registro de Preço.","incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }

    break;

}

Sessao::encerraExcecao();

?>