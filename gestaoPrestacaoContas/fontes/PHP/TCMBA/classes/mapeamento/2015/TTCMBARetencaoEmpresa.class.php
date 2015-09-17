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
/*
    * Arquivo de geracao do arquivo RetencaoEmpresa.txt TCM/BA
    * Data de Criação   : 11/09/2015
    * @author Analista      Valtair Santos
    * @author Desenvolvedor Michel Teixeira
    * 
    * $Id: TTCMBARetencaoEmpresa.class.php 63571 2015-09-11 14:04:58Z michel $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBARetencaoEmpresa extends Persistente {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaRetencaoEmpresa(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaRetencaoEmpresa().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRetencaoEmpresa()
    {
        $stSql = "                       
                SELECT to_char(nota_liquidacao_paga.timestamp,'yyyy') AS ano    
                     , empenho.exercicio AS ano_criacao
                     , pagamento_liquidacao.cod_ordem AS num_pagamento
                     , empenho.cod_empenho AS num_empenho   
                     , to_char(nota_liquidacao_paga.timestamp,'dd/mm/yyyy') AS dt_pagamento_empenho    
                     , REPLACE(plano_conta.cod_estrutural,'.','') AS conta_contabil
                     , conta_despesa.cod_estrutural AS desdobramento
                     , COALESCE(SUM(ordem_pagamento_retencao.vl_retencao),0.00) AS vl_retencao
                     , 1 AS tipo_registro
                     , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
                     , '".$this->getDado('competencia')."' AS competencia
                     , '0000' AS reservado
                     , '0000' AS reservado2
                     , '0000000000' AS reservado3

                 FROM empenho.empenho 

           INNER JOIN empenho.pre_empenho
                   ON empenho.exercicio = pre_empenho.exercicio    
                  AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho 

           INNER JOIN empenho.pre_empenho_despesa
                   ON pre_empenho.exercicio = pre_empenho_despesa.exercicio    
                  AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho   
                   
           INNER JOIN empenho.nota_liquidacao
                   ON empenho.exercicio = nota_liquidacao.exercicio_empenho    
                  AND empenho.cod_entidade = nota_liquidacao.cod_entidade    
                  AND empenho.cod_empenho = nota_liquidacao.cod_empenho  

           INNER JOIN empenho.pagamento_liquidacao
                   ON nota_liquidacao.exercicio = pagamento_liquidacao.exercicio    
                  AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade    
                  AND nota_liquidacao.cod_nota = pagamento_liquidacao.cod_nota 

           INNER JOIN empenho.ordem_pagamento
                   ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio    
                  AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade    
                  AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem 

           INNER JOIN empenho.ordem_pagamento_retencao
                   ON ordem_pagamento.exercicio = ordem_pagamento_retencao.exercicio    
                  AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade    
                  AND ordem_pagamento.cod_ordem = ordem_pagamento_retencao.cod_ordem 

           INNER JOIN empenho.nota_liquidacao_paga
                   ON nota_liquidacao.exercicio = nota_liquidacao_paga.exercicio    
                  AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade    
                  AND nota_liquidacao.cod_nota = nota_liquidacao_paga.cod_nota  

           INNER JOIN contabilidade.plano_analitica
                   ON ordem_pagamento_retencao.exercicio = plano_analitica.exercicio    
                  AND ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano 

           INNER JOIN contabilidade.plano_conta
                   ON plano_analitica.exercicio = plano_conta.exercicio    
                  AND plano_analitica.cod_conta = plano_conta.cod_conta                                                         

           INNER JOIN orcamento.despesa
                   ON pre_empenho_despesa.exercicio = despesa.exercicio    
                  AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa

           INNER JOIN orcamento.conta_despesa 
                   ON pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
                  AND pre_empenho_despesa.exercicio = conta_despesa.exercicio
                     
                 WHERE to_char(nota_liquidacao_paga.timestamp,'yyyy') = '".$this->getDado('exercicio')."'
                   AND to_date(to_char(nota_liquidacao_paga.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                                                      AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                   AND nota_liquidacao.cod_entidade IN (".$this->getDado('cod_entidade').")
                   --EXCETO FOLHA DE PAGAMENTO
                   AND conta_despesa.cod_estrutural NOT LIKE ('3.1%')

              GROUP BY to_char(nota_liquidacao_paga.timestamp,'dd/mm/yyyy')
                     , to_char(nota_liquidacao_paga.timestamp,'yyyy')
                     , empenho.exercicio
                     , empenho.cod_empenho
                     , plano_conta.cod_estrutural
                     , conta_despesa.cod_estrutural
                     , pagamento_liquidacao.cod_ordem

              ORDER BY pagamento_liquidacao.cod_ordem, num_empenho
                     , dt_pagamento_empenho

        ";
        return $stSql;
    }
    
}

?>