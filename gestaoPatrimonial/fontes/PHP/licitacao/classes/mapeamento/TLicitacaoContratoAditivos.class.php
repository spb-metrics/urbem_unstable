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
    * Classe de mapeamento da tabela licitacao.contrato_aditivos
    * Data de Criação: 30/10/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.05.22
*/
/*
$Log$
Revision 1.6  2007/10/11 21:30:32  girardi
adicionando ao repositório (rescisão de contrato e aditivos de contrato)

Revision 1.5  2006/11/27 12:03:30  leandro.zis
atualizado

Revision 1.4  2006/11/22 21:29:31  leandro.zis
atualizado

Revision 1.3  2006/11/10 18:35:54  leandro.zis
atualizado para o caso de uso 03.05.22

Revision 1.2  2006/11/08 10:51:42  larocca
Inclusão dos Casos de Uso

Revision 1.1  2006/11/01 19:56:38  leandro.zis
atualizado

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.contrato_aditivos
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoContratoAditivos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

function TLicitacaoContratoAditivos()
{
    parent::Persistente();
    $this->setTabela("licitacao.contrato_aditivos");

    $this->setCampoCod('num_aditivo');
    $this->setComplementoChave('num_contrato, exercicio, cod_entidade, exercicio_contrato');

    $this->AddCampo('num_aditivo', 'integer', true, '', true, false);
    $this->AddCampo('num_contrato', 'integer', true, '', true, true);
    $this->AddCampo('exercicio_contrato', 'char', true, '4', true, true);
    $this->AddCampo('exercicio', 'char', true, '4', true, false);
    $this->AddCampo('cod_entidade', 'integer', true, '', true, true);
    $this->AddCampo('responsavel_juridico', 'integer', true, '', false, true);
    $this->AddCampo('tipo_termo_aditivo', 'integer', true, '', false, true);
    $this->AddCampo('tipo_valor', 'integer', true, '', false, true);
    $this->AddCampo('dt_vencimento', 'date', true, '', false, false);
    $this->AddCampo('dt_assinatura', 'date', true, '', false, false);
    $this->AddCampo('inicio_execucao', 'date', true, '', false, false);
    $this->AddCampo('fim_execucao', 'date', true, '', false, false);
    $this->AddCampo('valor_contratado', 'numeric', true, '14,2', false, false);
    $this->AddCampo('objeto', 'char', true, '50', false, false);
    $this->AddCampo('justificativa', 'char', true, '250', false, false);
    $this->AddCampo('fundamentacao', 'char', true, '50', false, false);
}

function recuperaAditivos(&$rsRecordSet, $stFiltro = "",$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAditivos($stFiltro);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAditivos()
{
    $stSql = " select cod_norma, to_char(dt_vencimento, 'dd/mm/yyyy') as dt_vencimento, ";
    $stSql .= " num_aditivo from licitacao.contrato_aditivos ";
    $stSql .= " where num_contrato = ".$this->getDado('num_contrato')." ";
    $stSql .= "   and cod_entidade = ".$this->getDado('cod_entidade')." ";
    $stSql .= "   and exercicio = ".$this->getDado('exercicio')." ";

    return $stSql;
}

function recuperaContratosAditivosLicitacao(&$rsRecordSet, $stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosAditivosLicitacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosAditivosLicitacao()
{
    $stSQL = "\n SELECT contrato_aditivos.exercicio_contrato 
                      , contrato_aditivos.cod_entidade 
                      , contrato_aditivos.num_contrato 
                      , contrato_aditivos.exercicio as exercicio_aditivo 
                      , contrato_aditivos.num_aditivo 
                      , contrato_aditivos.responsavel_juridico
                      , contrato_aditivos.tipo_termo_aditivo
                      , contrato_aditivos.tipo_valor
                      , to_char(contrato_aditivos.dt_vencimento, 'dd/mm/yyyy') as dt_vencimento
                      , to_char(contrato_aditivos.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura 
                      , to_char(contrato_aditivos.inicio_execucao, 'dd/mm/yyyy') as inicio_execucao
                      , to_char(contrato_aditivos.fim_execucao, 'dd/mm/yyyy') as fim_execucao
                      , contrato_aditivos.valor_contratado
                      , contrato_aditivos.objeto
                      , contrato_aditivos.justificativa
                      , contrato_aditivos.fundamentacao
                      , contrato.cgm_contratado
                      , sw_cgm.nom_cgm 
                      , sw_cgm_responsavel_juridico.nom_cgm as cgm_responsavel_juridico
                      , tipo_termo_aditivo.descricao as descricao_termo_aditivo
                      , tipo_alteracao_valor.descricao as descricao_tipo_alteracao_valor
                      
                      FROM licitacao.contrato_aditivos 
               
                INNER JOIN licitacao.contrato 
                        ON contrato.exercicio = contrato_aditivos.exercicio_contrato 
                       AND contrato.cod_entidade = contrato_aditivos.cod_entidade 
                       AND contrato.num_contrato = contrato_aditivos.num_contrato
               
                INNER JOIN licitacao.contrato_licitacao
                        ON contrato_licitacao.num_contrato  = contrato.num_contrato
                       AND contrato_licitacao.cod_entidade  = contrato.cod_entidade
                       AND contrato_licitacao.exercicio  = contrato.exercicio
               
                INNER JOIN licitacao.licitacao
                        ON contrato_licitacao.cod_licitacao  = licitacao.cod_licitacao
                       AND contrato_licitacao.cod_entidade  = licitacao.cod_entidade
                       AND contrato_licitacao.exercicio  = licitacao.exercicio
                       AND contrato_licitacao.cod_modalidade  = licitacao.cod_modalidade
               
                INNER JOIN sw_cgm 
                        ON sw_cgm.numcgm = contrato.cgm_contratado
               
                INNER JOIN sw_cgm as sw_cgm_responsavel_juridico
                        ON sw_cgm_responsavel_juridico.numcgm = contrato_aditivos.responsavel_juridico
               
                INNER JOIN  orcamento.entidade
                        ON  contrato.cod_entidade = entidade.cod_entidade
                       AND contrato.exercicio = entidade.exercicio
               
                INNER JOIN sw_cgm AS cgm_entidade
                        ON entidade.numcgm = cgm_entidade.numcgm
                        
                 LEFT JOIN licitacao.tipo_termo_aditivo
                        ON tipo_termo_aditivo.cod_tipo = contrato_aditivos.tipo_termo_aditivo
                 
                 LEFT JOIN licitacao.tipo_alteracao_valor
                        ON tipo_alteracao_valor.cod_tipo = contrato_aditivos.tipo_valor  
               
                 WHERE ";

    if ($this->getDado("num_aditivo")) {
        $stSQL .= " contrato_aditivos.num_aditivo = ".$this->getDado("num_aditivo")." AND  ";
    }
    if ($this->getDado("cod_entidade")) {
        $stSQL .= " contrato_aditivos.cod_entidade = ".$this->getDado("cod_entidade")." AND  ";
    }
    if ($this->getDado("num_contrato")) {
        $stSQL .= " contrato_aditivos.num_contrato = ".$this->getDado("num_contrato")." AND  ";
    }
    if ($this->getDado("exercicio")) {
        $stSQL .= " contrato_aditivos.exercicio = '".$this->getDado("exercicio")."' AND  ";
    }
    if ($this->getDado("exercicio_contrato")) {
        $stSQL .= " contrato_aditivos.exercicio_contrato = '".$this->getDado("exercicio_contrato")."' AND  ";
    }

    $stSQL = substr($stSQL, 0, strlen($stSQL)-6);

    return $stSQL;
}

function recuperaContratosAditivosCompraDireta(&$rsRecordSet, $stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosAditivosCompraDireta",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosAditivosCompraDireta()
{
    $stSQL = "  SELECT  contrato_aditivos.exercicio_contrato 
                        , contrato_aditivos.cod_entidade 
                        , contrato_aditivos.num_contrato 
                        , contrato_aditivos.exercicio as exercicio_aditivo 
                        , contrato_aditivos.num_aditivo 
                        , contrato_aditivos.responsavel_juridico
                        , contrato_aditivos.tipo_termo_aditivo
                        , contrato_aditivos.tipo_valor
                        , to_char(contrato_aditivos.dt_vencimento, 'dd/mm/yyyy') as dt_vencimento
                        , to_char(contrato_aditivos.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura 
                        , to_char(contrato_aditivos.inicio_execucao, 'dd/mm/yyyy') as inicio_execucao
                        , to_char(contrato_aditivos.fim_execucao, 'dd/mm/yyyy') as fim_execucao
                        , contrato_aditivos.valor_contratado
                        , contrato_aditivos.objeto
                        , contrato_aditivos.justificativa
                        , contrato_aditivos.fundamentacao
                        , contrato.cgm_contratado
                        , sw_cgm.nom_cgm 
                        , sw_cgm_responsavel_juridico.nom_cgm as cgm_responsavel_juridico
                        , tipo_termo_aditivo.descricao as descricao_termo_aditivo
                        , tipo_alteracao_valor.descricao as descricao_tipo_alteracao_valor
                        
                      FROM licitacao.contrato_aditivos

                INNER JOIN licitacao.contrato 
                        ON contrato.exercicio = contrato_aditivos.exercicio_contrato 
                       AND contrato.cod_entidade = contrato_aditivos.cod_entidade 
                       AND contrato.num_contrato = contrato_aditivos.num_contrato 

                INNER JOIN licitacao.contrato_compra_direta
                        ON contrato_compra_direta.num_contrato  = contrato.num_contrato
                       AND contrato_compra_direta.cod_entidade  = contrato.cod_entidade
                       AND contrato_compra_direta.exercicio  = contrato.exercicio

                INNER JOIN compras.compra_direta
                        ON contrato_compra_direta.cod_compra_direta  = compra_direta.cod_compra_direta
                       AND contrato_compra_direta.cod_entidade  = compra_direta.cod_entidade
                       AND contrato_compra_direta.exercicio  = compra_direta.exercicio_entidade
                       AND contrato_compra_direta.cod_modalidade  = compra_direta.cod_modalidade

                INNER JOIN sw_cgm 
                        ON sw_cgm.numcgm = contrato.cgm_contratado

                INNER JOIN sw_cgm as sw_cgm_responsavel_juridico
                        ON sw_cgm_responsavel_juridico.numcgm = contrato_aditivos.responsavel_juridico

                INNER JOIN  orcamento.entidade
                        ON  contrato.cod_entidade = entidade.cod_entidade
                       AND contrato.exercicio = entidade.exercicio

                INNER JOIN  sw_cgm AS cgm_entidade
                        ON entidade.numcgm = cgm_entidade.numcgm
                
                 LEFT JOIN licitacao.tipo_termo_aditivo
                        ON tipo_termo_aditivo.cod_tipo = contrato_aditivos.tipo_termo_aditivo
                 
                 LEFT JOIN licitacao.tipo_alteracao_valor
                        ON tipo_alteracao_valor.cod_tipo = contrato_aditivos.tipo_valor  
                
               WHERE ";

    if ($this->getDado("num_aditivo")) {
        $stSQL .= " contrato_aditivos.num_aditivo = ".$this->getDado("num_aditivo")." AND  ";
    }
    if ($this->getDado("cod_entidade")) {
        $stSQL .= " contrato_aditivos.cod_entidade = ".$this->getDado("cod_entidade")." AND  ";
    }
    if ($this->getDado("num_contrato")) {
        $stSQL .= " contrato_aditivos.num_contrato = ".$this->getDado("num_contrato")." AND  ";
    }
    if ($this->getDado("exercicio")) {
        $stSQL .= " contrato_aditivos.exercicio = '".$this->getDado("exercicio")."' AND  ";
    }
    if ($this->getDado("exercicio_contrato")) {
        $stSQL .= " contrato_aditivos.exercicio_contrato = '".$this->getDado("exercicio_contrato")."' AND  ";
    }

    $stSQL = substr($stSQL, 0, strlen($stFiltro)-6);

    return $stSQL;
}

}
