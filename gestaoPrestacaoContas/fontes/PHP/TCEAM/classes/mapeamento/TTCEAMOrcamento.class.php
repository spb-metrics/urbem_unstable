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
    * Extensão da Classe de Mapeamento
    * Data de Criação: 04/03/2011
    *
    *
    * @author: Tonismar R. Bernardo
    *
    * @package URBEM
    *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEAMOrcamento extends Persistente
{
    /*
    * Método Constructor
    * @access Private
    */
    public function TTCEAMOrcamento()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaParametro(&$rsRecordSet, $stCondicao = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaParametro().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaParametro()
    {
        $stSql .= ' SELECT
                        valor AS '.($this->getDado('parametro') ? $this->getDado('parametro') : 'valor').'
                      FROM
                        administracao.configuracao_entidade
                     WHERE ';

        if ( $this->getDado('exercicio') ) {
            $stSql .= ' configuracao_entidade.exercicio = \''.$this->getDado('exercicio').'\'';
        }

        if ( $this->getDado('cod_modulo') ) {
            $stSql .= ' AND configuracao_entidade.cod_modulo = '.$this->getDado('cod_modulo');
        }

        if ( $this->getDado('cod_entidade') ) {
            $stSql .= ' AND configuracao_entidade.cod_entidade in ('.$this->getDado('cod_entidade').')';
        }

        if ( $this->getDado('parametro') ) {
            $stSql .= ' AND  configuracao_entidade.parametro = \''.$this->getDado('parametro').'\'';
        }

        return $stSql;
    }
}
?>
