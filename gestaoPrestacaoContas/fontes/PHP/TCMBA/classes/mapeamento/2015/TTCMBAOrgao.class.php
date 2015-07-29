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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBAOrgao extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMBAOrgao() {
      parent::Persistente();
      $this->setEstrutura( array() );
      $this->setEstruturaAuxiliar( array() );
      $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDados(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDados()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , '".$this->getDado('exercicio')."' AS ano
                        , ( SELECT valor
                              FROM administracao.configuracao_entidade
                             WHERE cod_modulo = 45
                               AND parametro = 'tceba_codigo_unidade_gestora'
                               AND cod_entidade = '".$this->getDado('entidade')."'
                          ) AS unidade_gestora
                        , orgao.num_orgao
                        , orgao.nom_orgao AS descricao
                        , (SELECT CASE WHEN sw_cgm.nom_cgm ilike '%prefeitura%' THEN 1
                                       WHEN sw_cgm.nom_cgm ilike '%camara%' THEN 2
                                       WHEN sw_cgm.nom_cgm ilike '%fundo%' THEN 4
                                       ELSE 3
                                  END AS tipo_poder
                             FROM administracao.configuracao_entidade
                       INNER JOIN orcamento.entidade
                               ON entidade.cod_entidade = configuracao_entidade.cod_entidade
                              AND entidade.exercicio = configuracao_entidade.exercicio
                       INNER JOIN sw_cgm
                               ON entidade.numcgm = sw_cgm.numcgm
                            WHERE configuracao_entidade.cod_entidade = '".$this->getDado('entidade')."'
                              AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                              AND cod_modulo = 45
                        ) AS tipo_poder
                        , sw_cgm_pessoa_fisica.cpf
                        , 0 AS tipo_gestao --necessário ver com a análise
                        , configuracao_ordenador.dt_inicio_vigencia AS dt_inicio_responsavel
                        , 0 AS tipo_responsavel --necessário ver com a análise

                    FROM orcamento.orgao

              INNER JOIN sw_cgm
                      ON sw_cgm.numcgm = orgao.usuario_responsavel

              INNER JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

              INNER JOIN tcmba.configuracao_ordenador
                      ON configuracao_ordenador.num_orgao = orgao.num_orgao
                     AND configuracao_ordenador.exercicio = orgao.exercicio

                   WHERE orgao.exercicio = '".$this->getDado('exercicio')."'

                   ORDER BY num_orgao
        ";
        
        return $stSql;
    }

}
