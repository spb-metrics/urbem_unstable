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
    * Data de Criação: 21/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26149 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-10-17 11:28:05 -0200 (Qua, 17 Out 2007) $

    * Casos de uso: uc-03.01.06
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemBaixado.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoBaixaPatrimonio.class.php";

$stPrograma = "ManterBaixarBem";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$obErro          = new Erro();
$boFlagTransacao = false;
$obTransacao     = new Transacao();
$stAcao          = $request->get('stAcao');

$obTPatrimonioBemBaixado = new TPatrimonioBemBaixado();
$obTPatrimonioBem        = new TPatrimonioBem();
$obTContabilidadeLancamentoBaixaPatrimonio = new TContabilidadeLancamentoBaixaPatrimonio();

switch ($stAcao) {
    case 'incluir':
        
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        
        if (!$obErro->ocorreu()) {
            
            //verifica se existe pelo menos um bem a ser baixado
            $arBem = Sessao::read('bens');

            if ( count( $arBem ) == 0 ) {
                $obErro->setDescricao("Você precisa baixar pelo menos 1 bem");
            } else {
                if ( implode('',array_reverse(explode('/',$request->get('dtBaixa')))) > date('Ymd') ) {
                    $obErro->setDescricao("A data de baixa deve ser menor ou igual a data de hoje");
                } else {
                    foreach ($arBem as $arTEMP) {
                        $obTPatrimonioBem->setDado( 'cod_bem', $arTEMP['cod_bem'] );
                        $obTPatrimonioBem->recuperaPorChave( $rsBem );
                        if ( implode('',array_reverse(explode('/',$rsBem->getCampo('dt_aquisicao')))) >  implode('',array_reverse(explode('/',$request->get('dtBaixa')))) ) {
                            $arBensInvalidos[] = $rsBem->getCampo('cod_bem');
                        }
                        
                        // Monta a string de bens a serem baixados.
                        $stCodBem .= $rsBem->getCampo('cod_bem').",";
                    }
                    if ( count( $arBensInvalidos ) > 1 ) {
                        $obErro->setDescricao("O(s) Bem(s) '.implode(',',$arBensInvalidos).' não foram baixados porque a data de aquisição é superior a data de baixa");
                    } elseif ( count( $arBensInvalidos ) == 1 ) {
                        $obErro->setDescricao("O Bem '.implode(',',$arBensInvalidos).' não foi baixado porque a data de aquisição é superior a data de baixa");
                    }
                }
            }
            
            if (!$obErro->ocorreu()) {
                
                // Faz lançamento autómatico somente para bens configurados como móveis ou imóveis.
                if ( $request->get('inTipoBaixa') != 0) {
                    
                    $stCodBem = substr($stCodBem,0,-1);
                    $obTPatrimonioBem->setDado('exercicio', Sessao::getExercicio());
                    $stGrupo = " GROUP BY grupo_plano_analitica.cod_plano
                                        , grupo_plano_analitica.cod_plano_doacao
                                        , grupo_plano_analitica.cod_plano_perda_involuntaria
                                        , grupo_plano_analitica.cod_plano_transferencia
                                        , natureza.cod_tipo
                                        , natureza.cod_natureza                 
                                        , natureza.nom_natureza
                                        , grupo.cod_grupo
                                        , grupo.nom_grupo ";
                    
                    $obTPatrimonioBem->recuperaContaContabil($rsContaContabil, " WHERE bem.cod_bem IN (".$stCodBem.") \n ", $stGrupo);
                    
                    // Verifica se está configurada um tipo de natureza para a natureza do Grupo
                    if ($rsContaContabil->getCampo('cod_tipo') == 0 || ($rsContaContabil->getCampo('cod_tipo') != 1 && $rsContaContabil->getCampo('cod_tipo') != 2) ) {
                        $obErro->setDescricao("Necessário configurar um Tipo de Natureza ( 1 - Bens móveis ou 2 - Bens imóveis ) para a Natureza: ".$rsContaContabil->getCampo('cod_natureza')." - ".$rsContaContabil->getCampo('nom_natureza'));
                    
                    // Verifica se está configurado um cod_plano contabil para o grupo
                    } else if ( is_null($rsContaContabil->getCampo('cod_plano')) ) {
                        $obErro->setDescricao("Necessário configurar uma Conta Contábil para o Grupo: ".$rsContaContabil->getCampo('cod_natureza')." - ".$rsContaContabil->getCampo('nom_natureza')." ".$rsContaContabil->getCampo('cod_grupo')." - ".$rsContaContabil->getCampo('nom_grupo'));
                    
                    // Verifica se está configurado um cod_plano contabil de baixa por doação para o grupo
                    } else if ( ($request->get('inTipoBaixa') == 1 || $request->get('inTipoBaixa') == 2) && is_null($rsContaContabil->getCampo('cod_plano_doacao') )) {
                        $obErro->setDescricao("Necessário configurar uma Conta Contábil de Baixa por Doação para o Grupo: ".$rsContaContabil->getCampo('cod_natureza')." - ".$rsContaContabil->getCampo('nom_natureza')." ".$rsContaContabil->getCampo('cod_grupo')." - ".$rsContaContabil->getCampo('nom_grupo'));
                    
                    // Verifica se está configurado um cod_plano contabil de baixa por transferência para o grupo
                    }else if ( ($request->get('inTipoBaixa') == 3 || $request->get('inTipoBaixa') == 4) && is_null($rsContaContabil->getCampo('cod_plano_transferencia') )) {
                        $obErro->setDescricao("Necessário configurar uma Conta Contábil de Baixa por Transferência para o Grupo: ".$rsContaContabil->getCampo('cod_natureza')." - ".$rsContaContabil->getCampo('nom_natureza')." ".$rsContaContabil->getCampo('cod_grupo')." - ".$rsContaContabil->getCampo('nom_grupo'));
                    
                    // Verifica se está configurado um cod_plano contabil de baixa por perda involuntária para o grupo
                    }else if ( ($request->get('inTipoBaixa') == 5 || $request->get('inTipoBaixa') == 6) && is_null($rsContaContabil->getCampo('cod_plano_perda_involuntaria') )) {
                        $obErro->setDescricao("Necessário configurar uma Conta Contábil de Baixa por Perda Involuntária para o Grupo: ".$rsContaContabil->getCampo('cod_natureza')." - ".$rsContaContabil->getCampo('nom_natureza')." ".$rsContaContabil->getCampo('cod_grupo')." - ".$rsContaContabil->getCampo('nom_grupo'));
                    }

                    if (!$obErro->ocorreu()){
                        $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'stExercicio'   , Sessao::getExercicio());
                        $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'stCodBem'      , $stCodBem);
                        $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'inTipoBaixa'   , $request->get('inTipoBaixa'));
                        $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'stDataBaixa'   , implode('-', array_reverse(explode('/',$request->get('dtBaixa')))));
                        $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'inCodHistorico', 966);
                        $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'stTipo'        , 'B');
                        $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'boEstorno'     , 'false');
                        
                        $obErro = $obTContabilidadeLancamentoBaixaPatrimonio->insereLancamentosBaixaPatrimonio($rsLancamentoBaixa, $boTransacao);
                    } else {
                        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                    }
                }
                
                if (!$obErro->ocorreu()){
                    //seta os dados e inclui
                    $obTPatrimonioBemBaixado->setDado( 'dt_baixa'   , $request->get('dtBaixa') );
                    $obTPatrimonioBemBaixado->setDado( 'motivo'     , $request->get('stMotivo') );
                    $obTPatrimonioBemBaixado->setDado( 'tipo_baixa' , $request->get('inTipoBaixa') );
                    
                    foreach ($arBem as $arTEMP) {
                        $obTPatrimonioBemBaixado->setDado( 'cod_bem', $arTEMP['cod_bem'] );
                        $obTPatrimonioBemBaixado->inclusao($boTransacao);
                        $arBens[] = $arTEMP['cod_bem'];
                    }
                    $stMsg = ( count( $arBens ) > 1 ) ? 'Bens: ' : 'Bem ';
                    $stMsg.= implode( ',',$arBens );
                    
                    $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTPatrimonioBemBaixado);
                    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,$stMsg,"incluir","aviso", Sessao::getId(), "../");
                }
                
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()).'!',"n_incluir","erro");
            }
        
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

        break;

    case 'excluir' :
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        
        if (!$obErro->ocorreu()) {
        
            $obTPatrimonioBemBaixado->recuperaRelacionamentoLancamento($rsBaixaLancamento, " WHERE bem.cod_bem = ".$request->get('inCodBem'));

            // Caso o bem pertença a há um dos tipos configurados como movél ou imóvel, mas não tenha sofrido um lançamento de baixa identificado pelo tipo de baixa, é necessário fazer o lançamento de forma manual
            if ( $rsBaixaLancamento->getCampo("tipo_baixa") == 0 && ($rsBaixaLancamento->getCampo("cod_tipo") == 1 || $rsBaixaLancamento->getCampo("cod_tipo") == 2)) {
                $obErro->setDescricao("O sistema não possui lançamento contábil vinculado ao bem ".$request->get('inCodBem').". Efetue lançamento contábil manual de estorno para este bem.");
            
            // Se o bem possuir um lancamento contábil, realiza o estorno, com os dados do exercicio que está fazendo o estorno.
            } else if ( $rsBaixaLancamento->getCampo("tipo_baixa") != 0 && $rsBaixaLancamento->getCampo("estorno") == "f" && ($rsBaixaLancamento->getCampo("cod_tipo") == 1 || $rsBaixaLancamento->getCampo("cod_tipo") == 2)) {
                $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'stExercicio'   , Sessao::getExercicio());
                $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'stCodBem'      , $rsBaixaLancamento->getCampo("cod_bem"));
                $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'inTipoBaixa'   , $rsBaixaLancamento->getCampo("tipo_baixa"));
                $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'stDataBaixa'   , $rsBaixaLancamento->getCampo("dt_baixa"));
                $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'inCodHistorico', 967);
                $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'stTipo'        , 'B');
                $obTContabilidadeLancamentoBaixaPatrimonio->setDado( 'boEstorno'     , 'true');
                $obErro = $obTContabilidadeLancamentoBaixaPatrimonio->insereLancamentosBaixaPatrimonio($rsEstornoBaixa, $boTransacao);
            }

            if ( !$obErro->ocorreu() ){
                
                $obTPatrimonioBemBaixado->setDado( 'cod_bem', $request->get('inCodBem') );
                $obTPatrimonioBemBaixado->exclusao($boTransacao);
                
                $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTPatrimonioBemBaixado);
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Bem ".$request->get('inCodBem'),"excluir","aviso", Sessao::getId(), "../");
                
            } else {
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,urlencode($obErro->getDescricao()),"excluir","aviso", Sessao::getId(), "../");
            }

        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

        break;
}

?>